<?php
// admin/action/edit_prispevek.php
require_once __DIR__ . '/../databaze.php';
header('Content-Type: application/json');

try {
    $id = (int)($_POST['blkt_id'] ?? 0);
    // Upravení hlavního záznamu
    $ok = blkt_update_prispevek($id, [
        'nazev'       => $_POST['blkt_nazev'],
        'kategorie'   => $_POST['blkt_kategorie'],
        'blkt_obsah'  => $_POST['blkt_obsah'],
    ]);


    // Upravení detailu (slug + štítky)
    $slug = $_POST['blkt_slug'] ?? '';
    $tags = $_POST['blkt_tags'] ?? '';
    if ($ok) {
        blkt_update_obsah_detail($id, 'post', $slug, $tags);
        echo json_encode([
            'status'  => 'ok',
            'message' => 'Příspěvek a detail upraveny.',
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'error'  => 'Nelze upravit příspěvek.',
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'error'  => $e->getMessage(),
    ]);
}
