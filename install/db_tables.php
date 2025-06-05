<?php
// install/db_tables.php
// Drop existujících tabulek a vytvoření nových

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Přístup zamítnut.']);
    exit;
}

// Načteme funkce pro práci s databází
require_once __DIR__ . '/../admin/databaze.php';

try {
    // Nejprve smažeme tabulky
    blkt_drop_table_prispevky();
    blkt_drop_table_konfigurace();
    blkt_drop_table_uzivatele();
    blkt_drop_table_obsah_detaily();

    // Pak vytvoříme tabulky
    blkt_create_table_uzivatele();
    blkt_create_table_konfigurace();
    blkt_create_table_prispevky();
    blkt_create_table_obsah_detaily();
    blkt_vytvor_tabku_obrazku();

    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Chyba při vytváření tabulek: ' . $e->getMessage()]);
}
exit;
?>
