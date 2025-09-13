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
$input = json_decode(file_get_contents("php://input"), true);
$product_id = intval($input["product_id"] ?? 0);
$quantity   = intval($input["quantity"] ?? 1);

if ($product_id <= 0 || $quantity <= 0) {
  echo json_encode(["success" => false, "message" => "Invalid product data"]);
  exit;
}

// Get stock and price for validation
$query = "SELECT product_price, stock_qty FROM product WHERE product_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$product) {
  echo json_encode([
    "success" => false, 
    "message" => "Product not found"
  ]);
  exit;
}

$maxStock     = intval($product["stock_qty"]);
$productPrice = $product["product_price"];

// Check if item already in cart
$query = "SELECT cart_item_id, cart_item_qty FROM cart_item WHERE cust_id = ? AND product_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $cust_id, $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {

  $existingQty = intval($row["cart_item_qty"]);
  $remaining   = $maxStock - $existingQty;

  if ($existingQty >= $maxStock) {
    echo json_encode([
      "success" => false,
      "message" => "You already have $maxStock item(s) in your cart."
    ]);
    mysqli_close($conn);
    exit;
  }

  if ($remaining <= 0) {
    echo json_encode([
      "success" => false,
      "message" => "Maximum stock reached ($maxStock). Cannot add more into cart."
    ]);
    mysqli_close($conn);
    exit;
  }

  if ($quantity > $remaining) {
    echo json_encode([
      "success" => false,
      "message" => "You already have $existingQty item(s) in your cart. Only $remaining more can be added.",
      "newQty"  => $existingQty
    ]);
    mysqli_close($conn);
    exit;
  }

  $newQty = $existingQty + $quantity;

  $query = "UPDATE cart_item SET cart_item_qty = ? WHERE cart_item_id = ? AND cust_id = ?";
  $stmtUpdate = mysqli_prepare($conn, $query);
  mysqli_stmt_bind_param($stmtUpdate, "iii", $newQty, $row["cart_item_id"], $cust_id);
  mysqli_stmt_execute($stmtUpdate);
  mysqli_stmt_close($stmtUpdate);

  echo json_encode([
    "success" => true,
    "message" => "Added $quantity item(s). Current quantity in cart: $newQty",
    "newQty"  => $newQty
  ]);
  mysqli_close($conn);
  exit;

} else {
  $finalQty = ($quantity > $maxStock) ? $maxStock : $quantity;

  $query = "INSERT INTO cart_item (cart_item_qty, cart_item_price, cart_status, cust_id, product_id) 
            VALUES (?, ?, 'pending', ?, ?)";
  $stmtInsert = mysqli_prepare($conn, $query);
  mysqli_stmt_bind_param($stmtInsert, "idii", $finalQty, $productPrice, $cust_id, $product_id);
  mysqli_stmt_execute($stmtInsert);
  mysqli_stmt_close($stmtInsert);

  echo json_encode([
    "success" => true,
    "message" => "Added $finalQty item(s) to cart successfully! Current quantity in cart: $finalQty",
    "newQty"  => $finalQty
  ]);
  mysqli_close($conn);
  exit;
}
?>