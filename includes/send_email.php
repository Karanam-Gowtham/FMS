<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer manually since composer is not available
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

function sendNotificationEmail($toEmail, $recipientName, $pendingCount, $role)
{
    if (empty($toEmail)) {
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'gowtham.lite@gmail.com';
        $mail->Password = 'uqyk efpk muqq usfa';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        //Recipients
        $mail->setFrom('gowtham.lite@gmail.com', 'FMS Notification System');
        $mail->addAddress($toEmail, $recipientName);

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Action Required: Pending Files in FMS Dashboard';

        $bodyContent = "<h2>Hello $recipientName,</h2>";
        $bodyContent .= "<p>You have <strong>$pendingCount</strong> file(s) pending in your dashboard.</p>";
        $bodyContent .= "<p>Please log in to your dashboard to review them.</p>";
        $bodyContent .= "<br><p>Thank you,<br>FMS Team</p>";

        $mail->Body = $bodyContent;
        $mail->AltBody = "Hello $recipientName, You have $pendingCount file(s) pending in your dashboard.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>