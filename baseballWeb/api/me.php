<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/db/connect.php';

if (!isset($_COOKIE['app_token'])) {
    http_response_code(401);
    echo json_encode(["error" => "Not authenticated"]);
    exit;
}

$token = $_COOKIE['app_token'];

$stmt = $pdo->prepare("SELECT id, username FROM users WHERE api_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid token"]);
    exit;
}

header("Content-Type: application/json");
echo json_encode($user);
