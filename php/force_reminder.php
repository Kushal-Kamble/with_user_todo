<?php
include("db.php");
include("PHPMailer/class.phpmailer.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $today = date('Y-m-d');

    $query = "SELECT t.id, u.email, u.name, t.title, t.deadline 
              FROM tasks t
              JOIN users u ON u.id = t.user_id
              WHERE t.completed = 0 
              AND t.user_id = ? 
              AND (t.last_reminder_sent IS NULL OR t.last_reminder_sent < ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    $res = $stmt->get_result();

    $sent = 0;
    while ($row = $res->fetch_assoc()) {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->Host = "email-smtp.us-east-1.amazonaws.com";
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
        $mail->Port = 2587;
        $mail->Username = "AKIA5OQ6466FZWEYNNVJ";
        $mail->Password = "BB8uQenn6fCEjW791mFxeUgQ39xwI/9PEBDPz7uasG58";
        $mail->SetFrom("kushal.kamble@mitsde.com", "ToDo Force Reminder");
        $mail->AddAddress($row['email'], $row['name']);
        $mail->Subject = "📬 Urgent Task Reminder: " . $row['title'];

        $body = "
        <div style='font-family:Arial; border:1px solid #ccc; padding:20px; border-radius:10px; max-width:600px; margin:auto;'>
            <h2 style='color:#003366;'>Forced Reminder 🔔</h2>
            <p><strong>Task:</strong> {$row['title']}</p>
            <p><strong>Deadline:</strong> {$row['deadline']}</p>
            <p>Please make sure to complete this task before deadline.</p>
            <hr>
            <p style='color:gray;'>This mail was sent by Force Reminder Button on your dashboard.</p>
        </div>";
        
        $mail->MsgHTML($body);
        if ($mail->Send()) {
            $conn->query("UPDATE tasks SET last_reminder_sent = '$today' WHERE id = {$row['id']}");
            $sent++;
        }
    }

    if ($sent > 0) {
        header("Location: ../dashboard.php?msg=Reminder sent for $sent task(s)");
    } else {
        header("Location: ../dashboard.php?msg=No tasks to remind or already reminded today");
    }
}
?>
