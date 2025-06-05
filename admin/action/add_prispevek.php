<?php
// admin/action/add_prispevek.php
require_once __DIR__ . '/../databaze.php';
header('Content-Type: application/json');

try {
    // 1) Vložíme příspěvek a uložíme jeho ID do proměnné
    $id = blkt_insert_prispevek([
        'nazev'       => $_POST['blkt_nazev'],
        'kategorie'   => $_POST['blkt_kategorie'],
        'blkt_obsah'  => $_POST['blkt_obsah'],
    ]);

    // --- DEBUG: odkomentujte, pokud si chcete ověřit v error_log, co vlastně dostáváme ---
    // error_log('Nové ID příspěvku: ' . var_export($id, true));

    // Pokud je ID 0 nebo prázdné, skončíme s chybou
    if (!$id) {
        throw new Exception('Chyba: blkt_insert_prispevek vrátil neplatné ID: ' . var_export($id, true));
    }

    // 2) Vložíme detail se slugem a štítky pod skutečné ID
    $slug = $_POST['blkt_slug'] ?? '';
    $tags = $_POST['blkt_tags'] ?? '';
    blkt_insert_obsah_detail((int)$id, 'post', $slug, $tags);

    echo json_encode([
        'status'  => 'ok',
        'message' => 'Příspěvek a detail přidány.',
        'id'      => $id,
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'error'  => $e->getMessage(),
    ]);
}
