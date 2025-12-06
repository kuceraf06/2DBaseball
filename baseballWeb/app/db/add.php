<?php
// Načtení připojení k databázi
require 'connect.php';

try {
    // SQL příkaz pro přidání sloupce
    $sql = "ALTER TABLE users ADD COLUMN api_token TEXT";

    // Spuštění
    $db->exec($sql);
    echo "Sloupec api_token byl úspěšně přidán.";
} catch (PDOException $e) {
    echo "Chyba: " . $e->getMessage();
}
