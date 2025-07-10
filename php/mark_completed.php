<?php
session_start();
include 'db.php';

if (isset($_POST['task_id'])) {
  $task_id = $_POST['task_id'];
  $user_id = $_SESSION['user']['id'];

  $stmt = $conn->prepare("UPDATE tasks SET completed = 1 WHERE id = ? AND user_id = ?");
  $stmt->bind_param("ii", $task_id, $user_id);
  $stmt->execute();
  echo "success";
}
?>
