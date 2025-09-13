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

// Remove item from cart
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
?>