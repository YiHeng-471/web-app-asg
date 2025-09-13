<?php
session_start();
header("Content-Type: application/json");
include_once("../config/config.php");

$cust_id = $_SESSION['cust_id'] ?? null;
if (!$cust_id) {
    echo json_encode([
        "success" => false, 
        "message" => "Not logged in"
    ]);
    exit;
}

//Add item to cart
if($_SERVER["REQUEST_METHOD"] === "POST") {
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
    mysqli_close($conn);
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
}

// Update item to cart
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
      mysqli_close($conn);
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
  mysqli_close($conn);
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
  mysqli_close($conn);
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
  mysqli_close($conn);
  exit;
}

echo json_encode([
  "success" => false,
  "message" => "Invalid action"
]);
mysqli_close($conn);
exit;
?>