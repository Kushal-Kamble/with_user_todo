<?php
include 'db.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=all_tasks.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Title', 'User', 'Priority', 'Start Date', 'Deadline', 'Status']);

$query = "SELECT tasks.id, tasks.title, users.name AS user, tasks.priority, tasks.start_date, tasks.deadline, 
          IF(tasks.completed = 1, 'Completed', 'Pending') AS status 
          FROM tasks 
          LEFT JOIN users ON tasks.user_id = users.id 
          ORDER BY tasks.deadline ASC";

$result = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($result)) {
  fputcsv($output, $row);
}
fclose($output);
exit();
?>
