<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../db/connect.php';
require __DIR__ . '/../config/send_mail.php';

header('Content-Type: application/json');

$email = trim($_POST['email'] ?? '');

if ($email === '') {
    echo json_encode(['success' => false, 'message' => 'Email cannot be empty']);
    exit;
}

$stmt = $db->prepare("SELECT id, username FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'This email is not associated with any account.']);
    exit;
}
$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', time() + 900);

$stmt = $db->prepare("
    INSERT OR REPLACE INTO password_resets (user_id, token, expires)
    VALUES (?, ?, ?)
");
$stmt->execute([$user['id'], $token, $expires]);

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$projectRoot = preg_replace('#/app/auth$#', '/', $scriptDir);
$rootUrl = $protocol . $host . $projectRoot;

$resetUrl = $rootUrl . "reset_password?token=$token";
$supportUrl = $rootUrl . 'support';

$mailSent = sendMail(
    $email,
    "2D Baseball Password Reset",
    "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Password Reset</title>
        <style>
            body { font-family: Arial, sans-serif; background-color: rgb(17, 17, 17); margin: 0; padding: 0; }
            .email-container { max-width: 600px; margin: 40px auto; background: rgb(17, 17, 17); border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
            .header { background-color: #275ea7; color: #eee; padding: 20px; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { padding: 30px; color: #eee; line-height: 1.5; }
            .content h2 { color: #275ea7; font-size: 20px; margin-top: 0; }
            .btn {color: #eee; text-decoration: underline; font-weight: bold; }
            .footer { padding: 15px; text-align: center; font-size: 12px; color: #777777; }
        </style>
    </head>
    <body>
        <div class='email-container'>
            <div class='header'>
                <h1>2D Baseball</h1>
            </div>
            <div class='content'>
            <h2>Hello {$user['username']},</h2>
            <p>We received a request to reset your password. Click the button below to reset it:</p>
            <a href='{$resetUrl}' class='btn'>Reset Password</a>
            <p>This link will expire in 15 minutes.</p>
            <p>If you did not request this, please contact our support team immediately <a href='{$supportUrl}' target='_blank' class='btn'>here</a>.</p>
        </div>
            <div class='footer'>
                &copy; " . date('Y') . " 2D Baseball — All Rights Reserved
            </div>
        </div>
    </body>
    </html>
    "
);

if ($mailSent) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send email']);
}
