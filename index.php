<!-- Check User login Status -->
<?php
session_start();
$loginStatus = isset($_SESSION['cust_id']) ? 'success' : 'fail';
?>

<!-- Home Page / Entry Point -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="styles.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <title>Home Page</title>
</head>

<body>
    <main>
        <div class="container">
            <?php include("header/index.php"); ?>

            <!-- Home Page Title -->
            <h1 class="title">Online Card Store</h1>
            <?php if ($loginStatus === 'success'): ?>
                <!-- Display Start Shopping button when user has logged in -->
                <a class="homepage-button" href="products/listing/index.php">Start Shopping</a>
            <?php else: ?>
                <!-- Display Login button when user is not logged in -->
                <a class="homepage-button" id="login-button" href="auth/login/index.php">Login !</a>
            <?php endif; ?>

            <?php include("footer/index.php"); ?>

        </div>
    </main>
</body>

</html>