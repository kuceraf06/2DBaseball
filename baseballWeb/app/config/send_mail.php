<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../lib/PHPMailer/src/Exception.php';
require __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../lib/PHPMailer/src/SMTP.php';

require __DIR__ . '/load_env.php';

function sendMail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = getenv("GMAIL_USERNAME");
        $mail->Password = getenv("GMAIL_APP_PASSWORD");
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = "UTF-8";

        $mail->setFrom(
            getenv("MAIL_FROM") ?: getenv("GMAIL_USERNAME"),
            getenv("MAIL_FROM_NAME") ?: "Mailer"
        );

        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        return $mail->send();
    } catch (Exception $e) {
        error_log("EMAIL ERROR: " . $e->getMessage());
        return false;
    }
}
