<?php
$host = "localhost:3309";
// $host = "localhost";
$user = "root";
$pass = "";
$dbname = "todo_ai_voice";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
