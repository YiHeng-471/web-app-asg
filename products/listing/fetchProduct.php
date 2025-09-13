<?php
session_start();
include_once ("../../config/config.php");

$search = $_GET["search"] ?? "";
$sort = $_GET["sortProduct"] ?? "product_name";
$order = $_GET["orderProduct"] ?? "ASC";
$filterCategory = $_GET["filterCategory"] ??  "";
$filterMinPrice = $_GET["filterMinPrice"] ?? "";
$filterMaxPrice = $_GET["filterMaxPrice"] ?? "";

$allowedSort = ["product_name", "product_price", "stock_qty", "category_name"];
$allowedOrder = ["ASC", "DESC"];

if (!in_array($sort, $allowedSort)) {
  $sort = "product_name";
}

if (!in_array($order,$allowedOrder)) {
  $order = "ASC";
}

$query = "SELECT p.product_id, p.product_name, p.product_img, p.product_price, p.stock_qty, c.category_name 
          FROM product as p INNER JOIN product_category as c 
          ON p.category_id = c.category_id
          WHERE p.stock_qty > 0"; 

$params = [];
$types = "";

//Search filter
if ($search !== "") {
  $query .= " AND (p.product_name LIKE ? OR c.category_name LIKE ?)";
  $searchTerm = "%$search%";
  $params[] = $searchTerm;
  $params[] = $searchTerm;
  $types .= "ss";
} 

//Category filter
if ($filterCategory != "" && is_numeric($filterCategory)) {
  $query .= " AND p.category_id = ?";
  $params[] = $filterCategory;
  $types .= "i";
}

//Min price filter
if ($filterMinPrice !== "" && is_numeric($filterMinPrice)) {
  $query .= " AND p.product_price >= ?";
  $params[] = (float) $filterMinPrice;
  $types .= "d";
}

//Max price filter
if ($filterMaxPrice !== "" && is_numeric($filterMaxPrice)) {
  $query .= " AND p.product_price <= ?";
  $params[] = (float) $filterMaxPrice;
  $types .= "d";
}

$query .= " ORDER BY $sort $order";

$stmt = mysqli_prepare($conn, $query);
if ($stmt == false) {
  die("MySQL prepare failed: " . mysqli_error($conn));
}

if (!empty($params)) {
  $refs = [];
  foreach ($params as $key => $value) {
    $refs[$key] = &$params[$key];
  }
  mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
  $products[] = $row;
}

$response = [
  "username" => $_SESSION["username"] ?? null,
  "products" => $products
];

mysqli_stmt_close($stmt);
mysqli_close($conn);

header ("Content-Type: application/json");
echo json_encode($response);
?>