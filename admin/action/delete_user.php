<?php
/* admin/action/delete_user.php */
// Smazání uživatele pomocí blkt_delete_uzivatel
header('Content-Type: application/json');
require_once __DIR__ . '/../databaze.php';

try {
    $id = isset($_POST['blkt_id']) ? (int) $_POST['blkt_id'] : 0;
    if (!$id) {
        throw new Exception('Neznámé ID uživatele.');
    }

    $ok = blkt_delete_uzivatel($id);
    if (!$ok) {
        throw new Exception('Nepodařilo se smazat uživatele.');
    }

    echo json_encode(['status' => 'ok', 'message' => "Uživatel (#{$id}) byl úspěšně smazán."]);
    exit;

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
    exit;
}