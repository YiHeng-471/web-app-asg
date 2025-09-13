<?php
session_start();
header("Content-Type: application/json");
include_once("../config/config.php");

// Check if user is logged in
$username = $_SESSION['username'] ?? null;
if (!$username) {
  echo json_encode(["success" => false, "message" => "Not logged in."]);
  exit;
} 

// Get customer ID
$query = "SELECT cust_id FROM customer WHERE username = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$cust_id = $row["cust_id"] ?? null;
mysqli_stmt_close($stmt);

if (!$cust_id) {
  echo json_encode(["success" => false, "message" => "User not found"]);
  exit;
} 

// Get input data
$product_id = intval($_GET['product_id']);
$quantity = intval($_GET['quantity']);


if ($product_id <= 0) {
  echo json_encode(["success" => false, "message" => "Invalid product data"]);
  exit;
}

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

// Update item quantity in cart
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

?>