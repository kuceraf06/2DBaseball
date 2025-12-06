<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/db/connect.php';

$headers = getallheaders();
$token = $headers['X-App-Token'] ?? null;

if (!$token) {
    http_response_code(401);
    echo json_encode(["error" => "Missing token"]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, username FROM users WHERE api_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid token"]);
    exit;
}

echo json_encode($user);
