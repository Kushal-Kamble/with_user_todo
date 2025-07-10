<?php
include("db.php");

$action = $_GET['action'] ?? '';

if ($action == 'get') {
  $user_id = $_GET['user_id'];
  $res = $conn->query("SELECT * FROM tasks WHERE user_id = $user_id ORDER BY deadline ASC");
  $tasks = [];
  while ($row = $res->fetch_assoc()) $tasks[] = $row;
  echo json_encode($tasks);
}

if ($action == 'save') {
  $id = $_POST['task_id'] ?? '';
  $uid = $_POST['user_id'];
  $title = $_POST['title'];
  $desc = $_POST['description'];
  $start = $_POST['start_date'];
  $end = $_POST['deadline'];
  $priority = $_POST['priority'];

  if ($id) {
    $conn->query("UPDATE tasks SET title='$title', description='$desc', start_date='$start', deadline='$end', priority='$priority' WHERE id=$id");
  } else {
    // Insert task
    $conn->query("INSERT INTO tasks (user_id, title, description, start_date, deadline, priority) 
      VALUES ('$uid', '$title', '$desc', '$start', '$end', '$priority')");

    // ✅ Send Email
    $userRes = $conn->query("SELECT * FROM users WHERE id = $uid");
    $user = $userRes->fetch_assoc();
    sendTaskMail($user['email'], $user['name'], $title, $desc, $end);
  }
}

if ($action == 'delete') {
  $id = $_POST['id'];
  $conn->query("DELETE FROM tasks WHERE id = $id");
}

if ($action == 'complete') {
  $id = $_POST['id'];
  $conn->query("UPDATE tasks SET completed=1 WHERE id = $id");
}

// 🔔 Email Function
function sendTaskMail($toEmail, $toName, $taskTitle, $taskDesc, $taskDeadline) {
  require_once('../PHPMailer/class.phpmailer.php');
  include("../PHPMailer/class.smtp.php");

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
  $mail->CharSet = 'UTF-8';
  $mail->Subject = "📝 New Task Added: $taskTitle";

  ob_start();
  ?>
  <div style="max-width:600px;margin:auto;border:1px solid #eaeaea;border-radius:8px;font-family:sans-serif">
    <div style="background:#003366;color:#fff;padding:20px;text-align:center;font-size:18px">
      🧠 MITSDE - Task Notification
    </div>
    <div style="padding:20px">
      <p>Dear <strong><?= htmlspecialchars($toName) ?></strong>,</p>
      <p>You have a new task added in your Todo system. Below are the details:</p>
      <table style="width:100%;border-collapse:collapse;margin:20px 0;">
        <tr><td style="padding:8px;font-weight:bold">Title:</td><td><?= htmlspecialchars($taskTitle) ?></td></tr>
        <tr><td style="padding:8px;font-weight:bold">Description:</td><td><?= htmlspecialchars($taskDesc) ?></td></tr>
        <tr><td style="padding:8px;font-weight:bold">Deadline:</td><td><?= htmlspecialchars($taskDeadline) ?></td></tr>
      </table>
      <p>Please complete it before the deadline. ⏳</p>
    </div>
    <div style="background:#f4f4f4;padding:15px;text-align:center;font-size:13px;color:#555">
      This is an automated reminder from your AI Task Manager. Do not reply.
    </div>
  </div>
  <?php
  $body = ob_get_clean();

  $mail->MsgHTML($body);
  $mail->AddAddress($toEmail);

  // Optional CC
  $mail->AddBCC("kushal.kamble@mitsde.com");

  $mail->Send(); // Don't show error to user; silent fail if needed
}
?>
