<?php
session_start();
header("Content-Type: application/json");
include_once("../config/config.php");

$username = $_SESSION['username'] ?? null;
if (!$username) {
    echo json_encode([
        "success" => false, 
        "message" => "Not logged in"
    ]);
    exit;
}

// Get customer ID
$stmt = $conn->prepare("SELECT cust_id FROM customer WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

$cust_id = $row['cust_id'] ?? null;
if (!$cust_id) {
    echo json_encode([
        "success" => false, 
        "message" => "User not found"
    ]);
    exit;
}

// Update item quantity
if (isset($_GET['action']) && $_GET['action'] === 'update' && isset($_GET['product_id']) && isset($_GET['quantity'])) {
    $product_id = intval($_GET['product_id']);
    $quantity = intval($_GET['quantity']);

    if ($quantity < 1) $quantity = 1;

    // Check stock
    $stmtStock = $conn->prepare("SELECT stock_qty FROM product WHERE product_id = ?");
    $stmtStock->bind_param("i", $product_id);
    $stmtStock->execute();
    $resStock = $stmtStock->get_result()->fetch_assoc();
    $stmtStock->close();

    if (!$resStock) {
        echo json_encode([
            "success" => false, 
            "message" => "Product not found"
        ]);
        exit;
    }
    $maxStock = intval($resStock['stock_qty']);
    if ($quantity > $maxStock) $quantity = $maxStock;

    $stmt = $conn->prepare("UPDATE cart_item SET cart_item_qty = ? WHERE cust_id = ? AND product_id = ?");
    $stmt->bind_param("iii", $quantity, $cust_id, $product_id);
    $success = $stmt->execute();
    $stmt->close();

    echo json_encode([
        "success" => $success,
        "message" => $success ? "Quantity updated." : "Failed to update quantity.",
        "newQty"  => $quantity
    ]);
    exit;
}

// Remove item from cart
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    $stmt = $conn->prepare("DELETE FROM cart_item WHERE cust_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $cust_id, $product_id);
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode([
        "success" => $success,
        "message" => $success ? "Item removed." : "Failed to remove item."
    ]);
    exit;
}

// Fetch cart items
if (!isset($_GET['action']) || $_GET['action'] === 'fetch') {
    $stmt = $conn->prepare(
        "SELECT ci.product_id, ci.cart_item_qty, p.product_name, p.product_price, p.product_img, p.stock_qty, c.category_name
         FROM cart_item ci
         INNER JOIN product p ON ci.product_id = p.product_id
         INNER JOIN product_category c ON p.category_id = c.category_id
         WHERE ci.cust_id = ?"
    );
    $stmt->bind_param("i", $cust_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $cartItems = [];
    $total = 0;
    while ($item = $result->fetch_assoc()) {
        $item['product_price'] = (float)$item['product_price'];
        $item['cart_item_qty'] = (int)$item['cart_item_qty'];
        $item['stock_qty']     = (int)$item['stock_qty'];
        $cartItems[] = $item;
        $total += $item['product_price'] * $item['cart_item_qty'];
    }
    $stmt->close();

    echo json_encode([
        "success"   => true,
        "cartItems" => $cartItems,
        "total"     => $total
    ]);
    exit;
}

echo json_encode([
    "success" => false,
    "message" => "Invalid action"
]);
exit;