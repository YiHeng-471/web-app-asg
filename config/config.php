  <?php
  $hostName = "localhost";
  $username = "root";
  $password = "password";
  $dbName = "php_asg";
  $port = 3306;

  $conn = new mysqli($hostName, $username, $password, $dbName, $port);

  if ($conn->connect_error) {
    die("Connection to MySQL has failed: " . $conn->connect_error);
  }
