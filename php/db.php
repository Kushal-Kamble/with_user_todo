<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "with_user_todo";
$port = 3309;

// $conn = mysqli_connect($host, $username, $password, $database);
$conn = mysqli_connect($host, $username, $password, $database, $port);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>
