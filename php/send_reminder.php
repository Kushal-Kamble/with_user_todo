<?php
require_once('db.php');

// ✅ Include PHPMailer
require_once('../PHPMailer/class.phpmailer.php');
require_once('../PHPMailer/class.smtp.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
  $taskId = intval($_POST['task_id']);

  // ✅ Get task and user info
  $query = "SELECT tasks.*, users.name as user_name, users.email as user_email 
            FROM tasks 
            JOIN users ON tasks.user_id = users.id 
            WHERE tasks.id = $taskId";
  $result = $conn->query($query);

  if ($result && $result->num_rows > 0) {
    $task = $result->fetch_assoc();
    $toEmail = $task['user_email'];
    $toName = $task['user_name'];
    $taskTitle = $task['title'];
    $taskDesc = $task['description'] ?? 'No description';
    $taskDeadline = $task['deadline'];

    // ✅ Send mail
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "tls";
    $mail->Host = "email-smtp.us-east-1.amazonaws.com";
    $mail->Port = 2587;
    $mail->Username = "AKIA5OQ6466FZWEYNNVJ";
    $mail->Password = "BB8uQenn6fCEjW791mFxeUgQ39xwI/9PEBDPz7uasG58";

    $mail->SetFrom('kushal.kamble@mitsde.com', 'MITSDE Todo Reminder');
    $mail->AddReplyTo('kushal.kamble@mitsde.com');
    $mail->AddAddress($toEmail);
    $mail->AddBCC("kushal.kamble@mitsde.com");

    $mail->CharSet = 'UTF-8';
    $mail->Subject = "⏰🔔 Reminder From Nitin Zadpe: Complete Task - $taskTitle";

    ob_start();
    ?>
    <div style="max-width:600px;margin:auto;border:1px solid #eaeaea;border-radius:8px;font-family:sans-serif">
      <div style="background:#003366;color:#fff;padding:20px;text-align:center;font-size:18px">
        🧠 MITSDE - Task Reminder
      </div>
      <div style="padding:20px">
        <p>Dear <strong><?= htmlspecialchars($toName) ?></strong>,</p>
        <p>This is a reminder to complete your pending task:</p>
        <table style="width:100%;border-collapse:collapse;margin:20px 0;">
          <tr><td style="padding:8px;font-weight:bold">Title:</td><td><?= htmlspecialchars($taskTitle) ?></td></tr>
          <tr><td style="padding:8px;font-weight:bold">Description:</td><td><?= htmlspecialchars($taskDesc) ?></td></tr>
          <tr><td style="padding:8px;font-weight:bold">Deadline:</td><td><?= htmlspecialchars($taskDeadline) ?></td></tr>
        </table>
        <p>Kindly complete the task before the deadline.</p>
      </div>
      <div style="background:#f4f4f4;padding:15px;text-align:center;font-size:13px;color:#555">
        This is an automated reminder from the Admin via AI Task Manager. Do not reply.
      </div>
    </div>
    <?php
    $body = ob_get_clean();
    $mail->MsgHTML($body);

    if ($mail->Send()) {
      echo 'success';
    } else {
      echo 'fail';
    }
  } else {
    echo 'invalid_task';
  }
} else {
  echo 'invalid_request';
}
?>
