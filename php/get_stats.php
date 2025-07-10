<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user']['id'];

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tasks WHERE user_id = $user_id");
$pendingQuery = mysqli_query($conn, "SELECT COUNT(*) AS pending FROM tasks WHERE user_id = $user_id AND completed = 0");
$completedQuery = mysqli_query($conn, "SELECT COUNT(*) AS completed FROM tasks WHERE user_id = $user_id AND completed = 1");

$response = [
  'total' => mysqli_fetch_assoc($totalQuery)['total'],
  'pending' => mysqli_fetch_assoc($pendingQuery)['pending'],
  'completed' => mysqli_fetch_assoc($completedQuery)['completed']
];

echo json_encode($response);
?>
