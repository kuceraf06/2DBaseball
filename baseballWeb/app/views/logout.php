<?php
session_start();
session_destroy();

setcookie("remember_me", "", time() - 3600, "/");

require __DIR__ . '/../db/connect.php';
$stmt = $db->prepare("UPDATE users SET remember_token=NULL, remember_expires=NULL WHERE id=:id");
$stmt->execute([":id" => $_SESSION['user_id'] ?? 0]);

header("Location: $baseUrl");
exit;
?>

