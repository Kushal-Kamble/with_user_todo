<?php

ob_start();



require_once('../PHPMailer/class.phpmailer.php');

include("../PHPMailer/class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded







// Retrieving form data

$yourname = $_POST['yourname'];

// $gender = $_POST['gender'];

$mobile = $_POST['mobile'];

$cname = $_POST['cname'];

$designation = $_POST['designation'];

$email = $_POST['email'];



$owenrMailID = "kushal.kamble@mitsde.com";



$mail  = new PHPMailer();

$mail->IsSMTP();

$mail->SMTPDebug  = 0;

$mail->SMTPAuth   = true;

$mail->SMTPSecure = "tls";

$mail->Host       = "email-smtp.us-east-1.amazonaws.com";

$mail->Port       = 2587;

$mail->Username   = "AKIA5OQ6466FZWEYNNVJ";

$mail->Password   = "BB8uQenn6fCEjW791mFxeUgQ39xwI/9PEBDPz7uasG58";



$mail->SetFrom('kushal.kamble@mitsde.com', 'Notification');

$mail->AddReplyTo('kushal.kamble@mitsde.com', 'Notification');

$mail->Subject    = "Todo Notification";



ob_start();

?>

<div style="max-width: 600px; margin: auto; font-family: Arial, sans-serif; border: 1px solid #e0e0e0; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

    <div style="background-color: #003366; color: #ffffff; padding: 20px; text-align: center; font-size: 20px; font-weight: bold;">

        MITSDE Todo Notification

    </div>

    <table style="width: 100%; border-collapse: collapse;">

        <tr>

            <td style="padding: 15px; background-color: #f8f8f8; font-weight: bold;"> Client Name:</td>

            <td style="padding: 15px; background-color: #f8f8f8;"><?php echo htmlspecialchars($yourname); ?></td>

        </tr>

        <!-- <tr>

            <td style="padding: 15px;">Gender:</td>

            <td style="padding: 15px;"><?php // echo htmlspecialchars($gender); ?></td>

        </tr> -->

        <tr>

            <td style="padding: 15px; background-color: #f8f8f8; font-weight: bold;">Mobile:</td>

            <td style="padding: 15px; background-color: #f8f8f8;"><?php  echo htmlspecialchars($mobile); ?></td>

        </tr>

        <tr>

            <td style="padding: 15px; background-color: #f8f8f8; font-weight: bold;">Company Name :</td>

            <td style="padding: 15px; background-color: #f8f8f8;"><?php echo htmlspecialchars($cname); ?></td>

        </tr>

        <tr>

            <td style="padding: 15px; background-color: #f8f8f8; font-weight: bold;">Designation :</td>

            <td style="padding: 15px; background-color: #f8f8f8;"><?php echo htmlspecialchars($designation); ?></td>

        </tr>

        <tr>

            <td style="padding: 15px; font-weight: bold;" >Email:</td>

            <td style="padding: 15px;"><?php echo htmlspecialchars($email); ?></td>

        </tr>

    </table>

    <div style="padding: 20px; font-size: 14px; color: #555; background-color: #f0f0f0; text-align: center;">

        Thank you for submitting your details.

    </div>

</div>

<?php

$body = ob_get_clean();



$mail->MsgHTML($body);

$mail->SetLanguage("en", 'includes/phpMailer/language/');

$mail->AddAddress($owenrMailID);



$ccEmails = ['kushal.kamble@mitsde.com'];

foreach ($ccEmails as $ccEmail) {

    $mail->AddBCC($ccEmail);

}



if ($mail->Send()) {

    header('Location: ../thankyou.php');

} else {

    echo "Email sending failed.";

}

?>

