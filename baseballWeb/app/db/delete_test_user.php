<?php
require __DIR__ . '/connect.php';

$emails = [
    'filipkucera06@gmail.com',
    'clapzxdd74@gmail.com',
    '2dbaseball25@gmail.com'
];

$placeholders = implode(',', array_fill(0, count($emails), '?'));

$stmt = $db->prepare("DELETE FROM users WHERE email IN ($placeholders)");
$stmt->execute($emails);

echo "Deleted rows: " . $stmt->rowCount();
