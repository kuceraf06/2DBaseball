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
$newUsername = trim($_POST['username'] ?? '');

if ($newUsername === '') {
    echo json_encode(['success' => false, 'message' => 'Username cannot be empty']);
    exit;
}

$stmt = $db->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$userId]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$currentUser) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$stmt = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
$stmt->execute([$newUsername, $userId]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Username already taken']);
    exit;
}

$stmt = $db->prepare("UPDATE users SET username = ? WHERE id = ?");
$success = $stmt->execute([$newUsername, $userId]);

if ($success) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST']; 
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $projectRoot = preg_replace('#/app/auth$#', '/', $scriptDir);
    $rootUrl = $protocol . $host . $projectRoot;
    $accountUrl = $rootUrl . 'account';
    $supportUrl = $rootUrl . 'support';

    $mailSent = sendMail(
        $currentUser['email'],
        "Your 2D Baseball username has been changed",
        "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Username Changed</title>
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
                    <h2>Hello {$newUsername}!</h2>
                    <p>Your username has been successfully changed from {$currentUser['username']} to $newUsername.</p>
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

    if (!$mailSent) {
        echo json_encode([
            'success' => true,
            'username' => $newUsername,
            'warning' => 'Username updated but confirmation email could not be sent'
        ]);
    } else {
        echo json_encode(['success' => true, 'username' => $newUsername]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
