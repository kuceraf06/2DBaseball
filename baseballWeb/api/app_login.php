<?php
header("Content-Type: application/json");
require __DIR__ . '/../app/db/connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$login = trim($data['login'] ?? '');
$password = $data['password'] ?? '';

if (!$login || !$password) {
    echo json_encode(["success" => false, "message" => "Missing credentials"]);
    exit;
}

$stmt = $db->prepare("SELECT id, username, password_hash FROM users
                       WHERE username = :login OR email = :login LIMIT 1");
$stmt->execute([":login" => $login]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user["password_hash"])) {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
    exit;
}

$token = bin2hex(random_bytes(32));

$stmt = $db->prepare("UPDATE users SET api_token = :t WHERE id = :id");
$stmt->execute([":t" => $token, ":id" => $user["id"]]);

echo json_encode([
    "success" => true,
    "token"   => $token,
    "user" => [
        "id" => $user["id"],
        "username" => $user["username"]
    ]
]);
