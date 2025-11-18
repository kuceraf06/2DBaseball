<?php
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

    echo json_encode(["success" => true]);

} catch (PDOException $e) {
    if (str_contains($e->getMessage(), "UNIQUE")) {
        echo json_encode(["success" => false, "message" => "Username or email already exists"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error"]);
    }
}
?>