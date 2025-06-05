<?php
// admin/action/delete_prispevek.php
require_once __DIR__ . '/../databaze.php';
header('Content-Type: application/json');

try {
    $id = (int)($_POST['blkt_id'] ?? 0);
    // Smažeme detail nejprve
    blkt_delete_obsah_detail($id, 'post');
    // Smažeme hlavní záznam
    $ok = blkt_delete_prispevek($id);

    if ($ok) {
        echo json_encode([
            'status'  => 'ok',
            'message' => 'Příspěvek a detail smazány.',
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'error'  => 'Nelze smazat příspěvek.',
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'error'  => $e->getMessage(),
    ]);
}
?>