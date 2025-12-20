<?php
require_once __DIR__ . '/connect.php';

function authUser() {
    global $db;

    $headers = getallheaders();
    $token = $headers['X-App-Token'] ?? null;

    if (!$token) {
        return false;
    }

    // najdi uživatele podle tokenu v databázi
    $stmt = $db->prepare("SELECT id, username FROM users WHERE api_token = :token LIMIT 1");
    $stmt->execute([":token" => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return false;
    }

    return $user;
}
