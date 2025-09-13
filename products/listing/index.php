<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kard Listing Page</title>
  <link rel="stylesheet" href="listing.css">
</head>
<header>
  
</header>
<body>
  <?php include("../../header/index.php"); ?>
  
  <h1>Cards</h1>
  
  <div id ="searchSortFilterContainer">
    <div id="searchContainer">
      <form method="get">
        <input type="text" name="search" placeholder="Search anything...">
        <button type="submit">Search</button> 
      </form>
    </div>

    <div id="filterContainer">
      <label for="filterCategory">Filter:</label>
      <select id="filterCategory" name="filterCategory">
        <option value="">All Categories</option>
      </select>

      <input type="number" name="filterMinPrice" id="filterMinPrice" placeholder="Min Price">
      <input type="number" name="filterMaxPrice" id="filterMaxPrice" placeholder="Max Price">
      <button id="btnFilterProduct">Filter</button>
    </div>

    <div id="sortContainer">
      <label for="sort">Sort by:</label>
      <select id="sortProduct" name="sortProduct">
        <option value="product_name">Name</option>
        <option value="product_price">Price</option>
        <option value="stock_qty">Quantity</option>
        <option value="category_name">Category</option>
      </select>

      <select id="orderProduct" name="orderProduct">
        <option value="ASC">Ascending</option>
        <option value="DESC">Descending</option>
      </select>

      <button id="btnSortProduct">Sort</button>
    </div>
  </div>


  <div id="productListMain">
    <div id="productListGrid"></div>
  </div>

  <?php include("../../footer/index.php"); ?>
  <script type="module" src="listing.js"></script>
</body>
</html>