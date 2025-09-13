<?php
session_start();
header("Content-Type: application/json");
include_once("../config/config.php");

// Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

// Check user login
$cust_id = $_SESSION['cust_id'] ?? null;
if (!$cust_id) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

// Get selected items from POST
$input = json_decode(file_get_contents("php://input"), true);
$items = $input['items'] ?? [];

if (empty($items)) {
    echo json_encode(["success" => false, "message" => "No items selected"]);
    exit;
}

// Get the user's address
$stmt = $conn->prepare("SELECT address FROM customer WHERE cust_id = ?");
$stmt->bind_param("i", $cust_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
$stmt->close();
$address = $customer['address'] ?? '';

// Initialize order variables
$order_status = "pending";
$total_amt = 0.00;

// Create new order
$stmt = $conn->prepare("INSERT INTO `order` (cust_id, order_date, address, order_status, total_amt) VALUES (?, NOW(), ?, ?, ?)");
$stmt->bind_param("issd", $cust_id, $address, $order_status, $total_amt);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

$total = 0;
foreach ($items as $item) {
    $product_id = intval($item['product_id']);
    $quantity = intval($item['quantity']);

    // Get product price and stock
    $stmt = $conn->prepare("SELECT product_price, stock_qty FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if (!$product || $product['stock_qty'] < $quantity) {
        echo json_encode(["success" => false, "message" => "Insufficient stock for product ID $product_id"]);
        exit;
    }

    $price = $product['product_price'];
    $subtotal = $price * $quantity;
    $total += $subtotal;

    // Insert into order_item
    $stmt = $conn->prepare("INSERT INTO order_item (order_item_qty, order_item_price, order_id, product_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idii", $quantity, $price, $order_id, $product_id);
    $stmt->execute();
    $stmt->close();

    // Update product stock
    $new_stock = $product['stock_qty'] - $quantity;
    $stmt = $conn->prepare("UPDATE product SET stock_qty = ? WHERE product_id = ?");
    $stmt->bind_param("ii", $new_stock, $product_id);
    $stmt->execute();
    $stmt->close();

    // Remove item from cart
    $stmt = $conn->prepare("DELETE FROM cart_item WHERE cust_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $cust_id, $product_id);
    $stmt->execute();
    $stmt->close();
}

// Update order total amount
$stmt = $conn->prepare("UPDATE `order` SET total_amt = ? WHERE order_id = ?");
$stmt->bind_param("di", $total, $order_id);
$stmt->execute();
$stmt->close();

echo json_encode([
    "success" => true,
    "message" => "Order placed successfully!",
    "order_id" => $order_id,
    "total" => $total
]);
exit;