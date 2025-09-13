<?php
session_start();
header("Content-Type: application/json");
include_once ("../../config/config.php");

$product_id = $_GET["id"] ?? null;

if (!$product_id) {
    echo json_encode(["success" => false, "message" => "Product Missing"]);
    exit;
}

$query="SELECT p.product_id, p.product_name, p.product_desc, p.product_img, p.product_price,  p.stock_qty, c.category_name
        FROM product AS p
        INNER JOIN product_category AS c ON  p.category_id = c.category_id
        WHERE p.product_id = ?";

$stmt=mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($product = mysqli_fetch_assoc($result)) {
    echo json_encode(["success" => true, "product" => $product]);
} else {
    echo json_encode(["success" => false, "message" => "Product not found"]);
}
?>