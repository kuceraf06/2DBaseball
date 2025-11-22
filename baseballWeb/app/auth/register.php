<?php
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";

$host = $_SERVER['HTTP_HOST']; 

$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$projectRoot = preg_replace('#/app/auth$#', '/', $scriptDir);

$rootUrl = $protocol . $host . $projectRoot;

$loginUrl = $rootUrl . 'login';
$supportUrl = $rootUrl . 'support';

header('Content-Type: application/json');

require __DIR__ . '/../db/connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$username || !$email || !$password) {
    echo json_encode(["success" => false, "message" => "Missing data"]);
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $db->prepare("
        INSERT INTO users (username, email, password_hash, created_at)
        VALUES (:username, :email, :hash, :created)
    ");

    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':hash' => $passwordHash,
        ':created' => date('Y-m-d H:i:s')
    ]);

    require __DIR__ . '/../config/send_mail.php';

    sendMail(
        $email,
        "Welcome to 2D Baseball!",
        "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Welcome to 2D Baseball!</title>
            <style>
                body { font-family: Arial, sans-serif; background-color: rgb(17, 17, 17); margin: 0; padding: 0; }
                .email-container { max-width: 600px; margin: 40px auto; background: rgb(17, 17, 17); border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .header { background-color: #275ea7; color: #eee; padding: 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { padding: 30px; color: #eee; line-height: 1.5; }
                .content h2 { color: #275ea7; font-size: 20px; margin-top: 0; }
                .btn {color: #eee; text-decoration: under-line; font-weight: bold; }
                .footer { padding: 15px; text-align: center; font-size: 12px; color: #777777; }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <h1>2D Baseball</h1>
                </div>
                <div class='content'>
                    <h2>Hello $username!</h2>
                    <p>Your account has been successfully created. We're excited to have you on board!</p>
                    <p>Click the button below to log in and start playing:</p>
                    <a href='{$loginUrl}' target='_blank' class='btn'>Login to Your Account</a>
                    <p>If you did not create this account, please ignore this email or contact out support team <a href='{$supportUrl}' target='_blank' class='btn'>here</a>.</p>
                </div>
                <div class='footer'>
                    &copy; " . date('Y') . " 2D Baseball — All Rights Reserved
                </div>
            </div>
        </body>
        </html>
        "
    );

    echo json_encode(["success" => true]);

} catch (PDOException $e) {
    if (str_contains($e->getMessage(), "UNIQUE")) {
        echo json_encode(["success" => false, "message" => "Username or email already exists"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error"]);
    }
}
?>