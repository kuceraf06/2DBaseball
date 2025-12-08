<?php
function dlog($msg) {
    file_put_contents(__DIR__ . "/debug.log", "[".date("H:i:s")."] " . $msg . "\n", FILE_APPEND);
}

$dbPath = __DIR__ . "/database.sqlite";

$db = new PDO("sqlite:" . $dbPath);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);