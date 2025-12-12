<?php
require_once __DIR__ . '/../app/db/connect.php';

$headers = getallheaders();
$token = $headers['X-App-Token'] ?? null;

dlog("me.php called with token: " . $token);

if (!$token) {
    http_response_code(401);
    echo json_encode(["error" => "Missing token"]);
    exit;
}

$stmt = $db->prepare("SELECT id, username FROM users WHERE api_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid token"]);
    exit;
}

echo json_encode($user);

if (!$user) {
    dlog("Invalid token in me.php: " . $token);
}
