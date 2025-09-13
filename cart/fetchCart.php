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

// Fetch cart items
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
?>