<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../db/connect.php';
header('Content-Type: application/json');

$token = trim($_POST['token'] ?? '');
$password = trim($_POST['password'] ?? '');
$passwordConfirm = trim($_POST['passwordConfirm'] ?? '');

if ($token === '' || $password === '' || $passwordConfirm === '') {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if ($password !== $passwordConfirm) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
    exit;
}

$stmt = $db->prepare("SELECT user_id, expires FROM password_resets WHERE token = ?");
$stmt->execute([$token]);
$reset = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reset || strtotime($reset['expires']) < time()) {
    echo json_encode(['success' => false, 'message' => 'Token is invalid or expired']);
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
$updated = $stmt->execute([$hash, $reset['user_id']]);

if ($updated) {
    $stmt = $db->prepare("DELETE FROM password_resets WHERE token = ?");
    $stmt->execute([$token]);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update password']);
}
exit;
