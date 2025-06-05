<?php
// Přidání nového obrázku

header('Content-Type: application/json');

require_once __DIR__ . '/../databaze.php';

try {
    if (!isset($_FILES['blkt_file'])) {
        throw new Exception('Nebyl nahrán žádný soubor.');
    }

    // Volání funkce, která zařídí upload i vložení do DB
    blkt_vloz_obrazek(
        $_FILES['blkt_file'],
        $_POST['blkt_title'] ?? '',
        $_POST['blkt_alt'] ?? '',
        $_POST['blkt_description'] ?? ''
    );

    echo json_encode(['status' => 'ok']);
    exit;
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
    exit;
}
