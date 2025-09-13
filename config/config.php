  <?php
  $hostName = "localhost";
  $username = "root";
  $password = "password";
  $dbName = "php_asg";
  // $port = 3306;

  // $conn = new mysqli($hostName, $username, $password, $dbName, $port);
  $conn = new mysqli($hostName, $username, $password, $dbName);

  if ($conn->connect_error) {
    die("Connection to MySQL has failed: " . mysqli_connect_error());
  }
