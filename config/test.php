<?php
include_once("./config.php");

// Function to handle query errors
function handleError($conn, $stmt, $table) {
    if ($stmt === false) {
        echo "Error preparing statement for $table: " . $conn->error . "<br>";
        $conn->close();
        exit();
    }
}

// 1. Insert into product_category
$categories = [
    ['Electronics'],
    ['Clothing'],
    ['Books']
];
$stmt = $conn->prepare("INSERT INTO product_category (category_name) VALUES (?)");
handleError($conn, $stmt, "product_category");
foreach ($categories as $category) {
    $stmt->bind_param("s", $category[0]);
    if ($stmt->execute()) {
        echo "Inserted category: {$category[0]}<br>";
    } else {
        echo "Error inserting category: " . $stmt->error . "<br>";
    }
}
$stmt->close();

// 2. Insert into customer (using password_hash for security)
$customers = [
    ['john_doe', 'password123', 'john@example.com', '1234567890', '123 Main St', '12345', 'CA'],
    ['jane_smith', 'securepass456', 'jane@example.com', '0987654321', '456 Oak Ave', '67890', 'NY']
];
$stmt = $conn->prepare("INSERT INTO customer (username, password, email, phone_number, address, postcode, state) VALUES (?, ?, ?, ?, ?, ?, ?)");
handleError($conn, $stmt, "customer");
foreach ($customers as $customer) {
    $hashedPassword = password_hash($customer[1], PASSWORD_DEFAULT);
    $stmt->bind_param("sssssss", $customer[0], $hashedPassword, $customer[2], $customer[3], $customer[4], $customer[5], $customer[6]);
    if ($stmt->execute()) {
        echo "Inserted customer: {$customer[0]}<br>";
    } else {
        echo "Error inserting customer: " . $stmt->error . "<br>";
    }
}
$stmt->close();

// 3. Insert into product
$products = [
    [1, 'Smartphone', 'High-end smartphone with 128GB storage', 'smartphone.jpg', 699.99, 50],
    [1, 'Laptop', '15-inch laptop with 16GB RAM', 'laptop.jpg', 1299.99, 30],
    [2, 'T-Shirt', 'Cotton graphic t-shirt', 'tshirt.jpg', 19.99, 100],
    [3, 'Novel', 'Bestseller fiction book', 'novel.jpg', 14.99, 200]
];
$stmt = $conn->prepare("INSERT INTO product (category_id, product_name, product_desc, product_img, product_price, stock_qty) VALUES (?, ?, ?, ?, ?, ?)");
handleError($conn, $stmt, "product");
foreach ($products as $product) {
    $stmt->bind_param("isssdi", $product[0], $product[1], $product[2], $product[3], $product[4], $product[5]);
    if ($stmt->execute()) {
        echo "Inserted product: {$product[1]}<br>";
    } else {
        echo "Error inserting product: " . $stmt->error . "<br>";
    }
}
$stmt->close();

// 4. Insert into order
$orders = [
    [1, '123 Main St', '2025-07-22 10:00:00', 'Pending', 719.98, 1],
    [2, '456 Oak Ave', '2025-07-22 11:00:00', 'Shipped', 14.99, 2]
];
$stmt = $conn->prepare("INSERT INTO `order` (cust_id, address, order_date, order_status, total_amt) VALUES (?, ?, ?, ?, ?)");
handleError($conn, $stmt, "order");
foreach ($orders as $order) {
    $stmt->bind_param("isssd", $order[0], $order[1], $order[2], $order[3], $order[4]);
    if ($stmt->execute()) {
        echo "Inserted order for customer ID: {$order[0]}<br>";
    } else {
        echo "Error inserting order: " . $stmt->error . "<br>";
    }
}
$stmt->close();

// 5. Insert into order_item
$orderItems = [
    [1, 1, 1, 699.99], // Order 1: 1 Smartphone
    [2, 1, 19.99, 2],  // Order 1: 1 T-Shirt
    [3, 2, 14.99, 1]   // Order 2: 1 Novel
];
$stmt = $conn->prepare("INSERT INTO order_item (product_id, order_id, order_item_price, order_item_qty) VALUES (?, ?, ?, ?)");
handleError($conn, $stmt, "order_item");
foreach ($orderItems as $item) {
    $stmt->bind_param("iidi", $item[0], $item[1], $item[2], $item[3]);
    if ($stmt->execute()) {
        echo "Inserted order item for order ID: {$item[1]}<br>";
    } else {
        echo "Error inserting order item: " . $stmt->error . "<br>";
    }
}
$stmt->close();

// 6. Insert into payment
$payments = [
    [1, '2025-07-22 10:05:00', 719.98, 'Credit Card', 'Completed', 1, 1],
    [2, '2025-07-22 11:05:00', 14.99, 'PayPal', 'Completed', 2, 2]
];
$stmt = $conn->prepare("INSERT INTO payment (order_id, cust_id, pay_date, pay_amt, pay_method, transaction_status) VALUES (?, ?, ?, ?, ?, ?)");
handleError($conn, $stmt, "payment");
foreach ($payments as $payment) {
    $stmt->bind_param("iisdss", $payment[0], $payment[1], $payment[2], $payment[3], $payment[4], $payment[5]);
    if ($stmt->execute()) {
        echo "Inserted payment for order ID: {$payment[0]}<br>";
    } else {
        echo "Error inserting payment: " . $stmt->error . "<br>";
    }
}
$stmt->close();

// 7. Insert into cart_item
$cartItems = [
    [1, 2, 19.99, 'Active', 1], // Customer 1: 2 T-Shirts
    [3, 1, 14.99, 'Active', 2]  // Customer 2: 1 Novel
];
$stmt = $conn->prepare("INSERT INTO cart_item (product_id, cart_item_qty, cart_item_price, cart_status, cust_id) VALUES (?, ?, ?, ?, ?)");
handleError($conn, $stmt, "cart_item");
foreach ($cartItems as $item) {
    $stmt->bind_param("iidsi", $item[0], $item[1], $item[2], $item[3], $item[4]);
    if ($stmt->execute()) {
        echo "Inserted cart item for customer ID: {$item[4]}<br>";
    } else {
        echo "Error inserting cart item: " . $stmt->error . "<br>";
    }
}
$stmt->close();

// 8. Insert into wishlist_item
$wishlistItems = [
    [1, 1, 1], // Customer 1: 1 Smartphone
    [2, 1, 2]  // Customer 2: 1 Laptop
];
$stmt = $conn->prepare("INSERT INTO wishlist_item (product_id, wishlist_item_qty, cust_id) VALUES (?, ?, ?)");
handleError($conn, $stmt, "wishlist_item");
foreach ($wishlistItems as $item) {
    $stmt->bind_param("iii", $item[0], $item[1], $item[2]);
    if ($stmt->execute()) {
        echo "Inserted wishlist item for customer ID: {$item[2]}<br>";
    } else {
        echo "Error inserting wishlist item: " . $stmt->error . "<br>";
    }
}
$stmt->close();

$conn->close();
echo "Database seeding completed!<br>";
?>