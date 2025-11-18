<?php
require_once __DIR__ . '/connect.php'; // DB připojení

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$type = $data['type'] ?? '';
$value = trim($data['value'] ?? '');

$response = ['available' => true]; // default true

if ($type && $value) {
    if ($type === 'username') {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    } elseif ($type === 'email') {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    } else {
        echo json_encode(['available' => false]);
        exit;
    }

    $stmt->execute([$value]);
    $response['available'] = $stmt->fetch() ? false : true;
} else {
    $response['available'] = false;
}

echo json_encode($response);
