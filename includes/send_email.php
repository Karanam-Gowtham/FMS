<?php
use PHPMailer\PHPMailer\PHPMailer;
// Load PHPMailer manually since composer is not available
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

// Load SMTP configuration from centralized config
require_once __DIR__ . '/../config.php';

function sendNotificationEmail($toEmail, $recipientName, $pendingCount)
{
    if (empty($toEmail)) {
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        //Server settings — credentials from config.php, not hard-coded
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = SMTP_AUTH;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;

        //Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
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
    } catch (\Exception $e) {
        return false;
    }
}
