<?php

require_once 'connect.php';

try {
    $db->exec("
        ALTER TABLE match_results
        ADD COLUMN team_a_score INT NOT NULL DEFAULT 0
    ");

    echo "team_a_score added\n";
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'Duplicate column')) {
        echo "team_a_score already exists\n";
    } else {
        throw $e;
    }
}

try {
    $db->exec("
        ALTER TABLE match_results
        ADD COLUMN team_b_score INT NOT NULL DEFAULT 0
    ");

    echo "team_b_score added\n";
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'Duplicate column')) {
        echo "team_b_score already exists\n";
    } else {
        throw $e;
    }
}

echo "Migration finished\n";
