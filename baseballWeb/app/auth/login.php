<?php
header('Content-Type: application/json');

require __DIR__ . '/../db/connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$loginId = trim($data['loginId'] ?? '');
$password = $data['password'] ?? '';
$remember = $data['remember'] ?? false;

if (!$loginId || !$password) {
    echo json_encode(["success" => false, "message" => "Missing credentials"]);
    exit;
}

$stmt = $db->prepare("
    SELECT id, username, email, password_hash
    FROM users
    WHERE username = :login OR email = :login
    LIMIT 1
");
$stmt->execute([":login" => $loginId]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["success" => false, "message" => "User not found"]);
    exit;
}

if (!password_verify($password, $user["password_hash"])) {
    echo json_encode(["success" => false, "message" => "Incorrect password"]);
    exit;
}

session_start();
$_SESSION['user_id'] = $user["id"];
$_SESSION['username'] = $user["username"];

if ($remember) {
    $token = bin2hex(random_bytes(32));
    $expires = time() + 86400 * 30;

    $stmt = $db->prepare("
        UPDATE users SET remember_token = :token, remember_expires = :expires
        WHERE id = :id
    ");
    $stmt->execute([
        ":token" => $token,
        ":expires" => date("Y-m-d H:i:s", $expires),
        ":id" => $user["id"]
    ]);

    setcookie("remember_me", $token, $expires, "/", "", false, true);
}

echo json_encode(["success" => true]);
