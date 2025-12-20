<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once __DIR__ . '/../app/db/connect.php';
require_once __DIR__ . '/../app/db/auth.php';

$user = authUser();
if (!$user) {
    http_response_code(401);
    echo json_encode(["message" => "Not authorized"]);
    exit;
}

try {

    // nejdriv pokus o insert
    $stmt = $db->prepare("
        INSERT OR IGNORE INTO user_stats (user_id, matches_played)
        VALUES (:uid, 0)
    ");
    $stmt->execute([":uid" => $user['id']]);

    // potom update
    $stmt = $db->prepare("
        UPDATE user_stats
        SET matches_played = matches_played + 1
        WHERE user_id = :uid
    ");
    $stmt->execute([":uid" => $user['id']]);

    echo json_encode(["success" => true]);

} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
