<?php
require 'connect.php';

$userIds = [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,49,51,52];

try {
    $stmt = $db->prepare("
        INSERT INTO user_stats (user_id, matches_played)
        VALUES (:user_id, :matches_played)
    ");

    foreach ($userIds as $id) {
        $stmt->execute([
            ':user_id' => $id,
            ':matches_played' => 0
        ]);
    }

    echo "Záznamy byly úspěšně vloženy do user_stats.";
} catch (PDOException $e) {
    echo "Chyba: " . $e->getMessage();
}
