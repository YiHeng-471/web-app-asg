<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Kart</title>
    <link rel="stylesheet" href="cart.css">
</head>
<body>
  <?php include("../header/index.php"); ?>

  <div class="cart-container">
    <h2>Your Shopping Cart</h2>
    <div id="cartContent"></div>
  </div>

  <div class="continue-shopping-links">
    <a href="../products/listing/index.php">
      Continue Shopping
    </a>    
  </div>

  <?php include("../footer/index.php"); ?>
  <script src="cart.js"></script>
</body>
</html>