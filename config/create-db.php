<?php
$hostName = "localhost";
$username = "root";
$password = "password";
// $port = 3306;

// $conn = new mysqli($hostName, $username, $password, null, $port);
$conn = new mysqli($hostName, $username, $password, null);

if (mysqli_connect_errno()) {
  die("Connection to MySQL has failed: " . $conn->connect_error);
}

$dbName = "php_asg";

$dropDb = "DROP DATABASE IF EXISTS `$dbName`";

if (mysqli_query($conn, $dropDb)) {
  echo "DB dropped successfully!<br>";
} else {
  echo "Error dropping db: " . $conn->error;
}

$createDb = "CREATE DATABASE IF NOT EXISTS `$dbName`";

if (mysqli_query($conn, $createDb)) {
  echo "DB created successfully<br>";
} else {
  echo "Error dropping db: " . $conn->error;
}

mysqli_close($conn);
