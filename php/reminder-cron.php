<?php
include("db.php");
include("PHPMailer/class.phpmailer.php");

// Fetch incomplete tasks whose deadline is within 1 day
$tomorrow = date('Y-m-d', strtotime('+1 day'));

$query = "SELECT u.email, u.name, t.title, t.deadline FROM tasks t
JOIN users u ON u.id = t.user_id
WHERE t.completed = 0 AND t.deadline = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $tomorrow);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Host = "email-smtp.us-east-1.amazonaws.com";
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "tls";
    $mail->Port = 2587;
    $mail->Username = "AKIA5OQ6466FZWEYNNVJ";
    $mail->Password = "BB8uQenn6fCEjW791mFxeUgQ39xwI/9PEBDPz7uasG58";
    $mail->SetFrom("kushal.kamble@mitsde.com", "ToDo Reminder");
    $mail->AddAddress($row['email'], $row['name']);
    $mail->Subject = "⏰ Task Reminder: " . $row['title'];
    
    $body = "
    <div style='font-family:Arial; border:1px solid #ddd; border-radius:8px; padding:20px; max-width:600px; margin:auto;'>
        <h2 style='color:#003366;'>Reminder for your Task</h2>
        <p><strong>Task:</strong> {$row['title']}</p>
        <p><strong>Deadline:</strong> {$row['deadline']}</p>
        <p style='color:red;'>Please complete it on time.</p>
        <p>Thanks,<br>ToDo AI Bot</p>
    </div>";
    
    $mail->MsgHTML($body);
    $mail->Send();
}
?>
