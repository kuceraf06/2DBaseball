<?php
require_once __DIR__ . '/../app/db/connect.php';

if (!isset($_GET['user_id'])) {
    echo json_encode([]);
    exit;
}

$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$stmt = $db->prepare("SELECT result, team_a_score, team_b_score FROM match_results WHERE user_id = :uid ORDER BY id DESC");
$stmt->execute([':uid' => $userId]);
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($matches);
