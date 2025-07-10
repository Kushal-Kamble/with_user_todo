<?php
session_start();
require_once 'db.php'; // Your DB connection file

$user_id = $_SESSION['user']['id'] ?? 0;

$sql = "SELECT 
            COUNT(*) as total, 
            SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed, 
            SUM(CASE WHEN completed = 0 THEN 1 ELSE 0 END) as pending 
        FROM tasks 
        WHERE user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($data);
