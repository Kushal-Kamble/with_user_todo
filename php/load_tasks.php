<?php
session_start();
include("db.php");

$user_id = $_SESSION['user']['id'];

$sql = "SELECT * FROM tasks WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$tasks = [];
while ($row = $res->fetch_assoc()) {
    $tasks[] = $row;
}

echo json_encode($tasks);
?>
