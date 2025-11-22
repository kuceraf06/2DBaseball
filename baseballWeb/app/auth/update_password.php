<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

set_exception_handler(function($e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
});

set_error_handler(function($severity, $message, $file, $line) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
});

session_start();
require_once __DIR__ . '/../db/connect.php'; 
require __DIR__ . '/../config/send_mail.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];

$current = $_POST['current'] ?? '';
$new = $_POST['new'] ?? '';

if(empty($current) || empty($new)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if(strlen($new) < 8 || !preg_match('/[A-Z]/', $new) || !preg_match('/[0-9]/', $new)) {
    echo json_encode(['success' => false, 'message' => 'New password must be stronger.']);
    exit;
}

$stmt = $db->prepare("SELECT username, email, password_hash FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

if(!password_verify($current, $user['password_hash'])) {
    echo json_encode(['success' => false, 'message' => 'Current password incorrect.']);
    exit;
}

$newHash = password_hash($new, PASSWORD_DEFAULT);

$update = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
if($update->execute([$newHash, $userId])) {

    // --- send confirmation email ---
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST']; 
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $projectRoot = preg_replace('#/app/auth$#', '/', $scriptDir);
    $rootUrl = $protocol . $host . $projectRoot;
    $accountUrl = $rootUrl . 'account';
    $supportUrl = $rootUrl . 'support';

    $mailSent = sendMail(
        $user['email'],
        "Your 2D Baseball password has been changed",
        "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Password Changed</title>
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
                    <h2>Hello {$user['username']}!</h2>
                    <p>Your password has been successfully changed.</p>
                    <p>If you did not authorize this change, please immediately update your password
                    <a href='{$accountUrl}' target='_blank' class='btn'>here</a> and contact our support team
                    <a href='{$supportUrl}' target='_blank' class='btn'>here</a>.</p>
                </div>
                <div class='footer'>
                    &copy; " . date('Y') . " 2D Baseball — All Rights Reserved
                </div>
            </div>
        </body>
        </html>
        "
    );

    if(!$mailSent){
        echo json_encode(['success' => true, 'warning' => 'Password updated but confirmation email could not be sent']);
    } else {
        echo json_encode(['success' => true]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update password.']);
}
