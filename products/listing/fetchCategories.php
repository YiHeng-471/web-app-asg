<?php
session_start();
include_once ("../../config/config.php");

$query = "SELECT category_id, category_name
          FROM product_category 
          ORDER BY category_name ASC";

$result = mysqli_query($conn,$query);

$categories = [];
while($row = mysqli_fetch_assoc($result)) {
  $categories[] = $row;
}

header("Content-Type: application/json");
echo json_encode($categories);
mysqli_close($conn);
?>