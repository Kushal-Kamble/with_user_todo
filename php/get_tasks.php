<?php
session_start();
require 'db.php';

$userId = $_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

while ($task = $result->fetch_assoc()) {
  $priorityBadge = $task['priority'] === 'High' ? 'danger' : ($task['priority'] === 'Medium' ? 'warning' : 'success');
  $status = $task['completed'] ? '✅ Completed' : '❌ Pending';

  echo "<div class='col-md-4 mb-3'>
    <div class='card shadow-sm'>
      <div class='card-body'>
        <h5>{$task['title']} <span class='badge bg-$priorityBadge'>{$task['priority']}</span></h5>
        <p>{$task['description']}</p>
        <p><strong>Start:</strong> {$task['start_date']} | <strong>Deadline:</strong> {$task['deadline']}</p>
        <p>Status: $status</p>";

  if (!$task['completed']) {
    echo "<button class='btn btn-outline-success btn-sm completeBtn' data-id='{$task['id']}'>Mark Completed</button>";
  }

  echo "
        <button class='btn btn-outline-primary btn-sm editBtn' data-task='" . json_encode($task) . "'>Edit</button>
        <button class='btn btn-outline-danger btn-sm deleteBtn' data-id='{$task['id']}'>Delete</button>
      </div>
    </div>
  </div>";
}
