<?php
session_start();
require_once __DIR__ . '/../db/connect.php';
require __DIR__ . '/../config/send_mail.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$password = $_POST['password'] ?? '';

if ($password === '') {
    echo json_encode(['success' => false, 'message' => 'Password required']);
    exit;
}

$stmt = $db->prepare("SELECT username, email, password_hash FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

if (!password_verify($password, $user['password_hash'])) {
    echo json_encode(['success' => false, 'message' => 'Incorrect password']);
    exit;
}

$delete = $db->prepare("DELETE FROM users WHERE id = ?");
if (!$delete->execute([$userId])) {
    echo json_encode(['success' => false, 'message' => 'Failed to delete account']);
    exit;
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST']; 
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$projectRoot = preg_replace('#/app/auth$#', '/', $scriptDir);
$rootUrl = $protocol . $host . $projectRoot;
$supportUrl = $rootUrl . 'support';

sendMail(
    $user['email'],
    "Your 2D Baseball account has been deleted",
    "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Account Deleted</title>
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
                <p>Hello {$user['username']},</p>
                <p>Your account has been permanently deleted. This action cannot be undone.</p>
                <p>It was a pleasure having you with us. If you have questions, contact support 
                <a href='{$supportUrl}' target='_blank' class='btn'>here</a>.</p>
            </div>
            <div class='footer'>
                &copy; ".date('Y')." 2D Baseball — All Rights Reserved
            </div>
        </div>
    </body>
    </html>
    "
);

session_destroy();

echo json_encode(['success' => true]);
