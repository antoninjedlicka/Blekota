<?php
/* admin/action/edit_user.php */
// Upravení existujícího uživatele pomocí blkt_update_uzivatel
header('Content-Type: application/json');
require_once __DIR__ . '/../databaze.php';

try {
    $id       = isset($_POST['blkt_id']) ? (int) $_POST['blkt_id'] : 0;
    $jmeno    = trim($_POST['blkt_jmeno'] ?? '');
    $prijmeni = trim($_POST['blkt_prijmeni'] ?? '');
    $mail     = trim($_POST['blkt_mail'] ?? '');
    $admin    = isset($_POST['blkt_admin']) ? (int) $_POST['blkt_admin'] : 0;

    if (!$id) {
        throw new Exception('Neznámé ID uživatele.');
    }
    if (!$jmeno || !$prijmeni || !$mail) {
        throw new Exception('Všechna pole jsou povinná.');
    }
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Neplatný formát e-mailu.');
    }

    $data = [
        'jmeno'    => $jmeno,
        'prijmeni' => $prijmeni,
        'mail'     => $mail,
        'stav'     => 1,
        'admin'    => $admin,
    ];
    $ok = blkt_update_uzivatel($id, $data);
    if (!$ok) {
        throw new Exception('Nepodařilo se aktualizovat uživatele.');
    }

    echo json_encode(['status' => 'ok', 'message' => "Uživatel (#{$id}) byl úspěšně upraven."]);
    exit;

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
    exit;
}