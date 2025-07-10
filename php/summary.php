<?php
include 'db.php';

$summarySql = "SELECT 
  COUNT(*) as total, 
  SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed,
  SUM(CASE WHEN completed = 0 THEN 1 ELSE 0 END) as pending
FROM tasks";

$res = mysqli_query($conn, $summarySql);
$data = mysqli_fetch_assoc($res);

echo json_encode($data);
?>
