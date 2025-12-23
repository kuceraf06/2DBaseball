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

$data = json_decode(file_get_contents("php://input"), true);


if (
    !isset($data['team_a_score']) ||
    !isset($data['team_b_score'])
) {
    http_response_code(400);
    echo json_encode(["error" => "Missing score data"]);
    exit;
}

$teamAScore = (int) $data['team_a_score'];
$teamBScore = (int) $data['team_b_score'];

if ($teamAScore < 0 || $teamBScore < 0) {
    http_response_code(400);
    echo json_encode(["error" => "Score must be >= 0"]);
    exit;
}

if ($teamBScore > $teamAScore) {
    $result = 'WIN';
} elseif ($teamBScore < $teamAScore) {
    $result = 'LOSE';
} else {
    $result = 'TIED';
}

try {

    $stmt = $db->prepare("
        INSERT INTO match_results (
            user_id,
            result,
            team_a_score,
            team_b_score
        ) VALUES (
            :uid,
            :result,
            :team_a,
            :team_b
        )
    ");

    $stmt->execute([
        ":uid"    => $user['id'],
        ":result" => $result,
        ":team_a" => $teamAScore,
        ":team_b" => $teamBScore
    ]);

    echo json_encode([
        "success" => true,
        "result"  => $result
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
