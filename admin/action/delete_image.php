<?php
// Smazání obrázku

header('Content-Type: application/json');

require_once __DIR__ . '/../databaze.php';

try {
    if (!isset($_POST['blkt_id'])) {
        throw new Exception('Chybí ID obrázku.');
    }

    $id = (int)$_POST['blkt_id'];

    // Funkce smaže soubor i DB záznam
    $ok = blkt_smaz_obrazek($id);

    if (!$ok) {
        throw new Exception('Nepodařilo se smazat obrázek.');
    }

    echo json_encode(['status' => 'ok']);
    exit;
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
    exit;
}
