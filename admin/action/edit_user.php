<?php
/* admin/action/edit_user.php */
header('Content-Type: application/json');
require_once __DIR__ . '/../databaze.php';

try {
    $id       = isset($_POST['blkt_id']) ? (int) $_POST['blkt_id'] : 0;
    $jmeno    = trim($_POST['blkt_jmeno'] ?? '');
    $prijmeni = trim($_POST['blkt_prijmeni'] ?? '');
    $mail     = trim($_POST['blkt_mail'] ?? '');
    $heslo    = $_POST['blkt_heslo'] ?? '';
    $admin    = isset($_POST['blkt_admin']) ? (int) $_POST['blkt_admin'] : 0;
    $idskupiny = !empty($_POST['blkt_idskupiny']) ? (int)$_POST['blkt_idskupiny'] : null;

    if (!$id) {
        throw new Exception('Neznámé ID uživatele.');
    }
    if (!$jmeno || !$prijmeni || !$mail) {
        throw new Exception('Všechna pole kromě hesla jsou povinná.');
    }
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Neplatný formát e-mailu.');
    }

    $data = [
        'jmeno'      => $jmeno,
        'prijmeni'   => $prijmeni,
        'mail'       => $mail,
        'stav'       => 1,
        'admin'      => $admin,
        'idskupiny'  => $idskupiny,
    ];

    // Přidáme heslo pouze pokud bylo zadáno
    if (!empty($heslo)) {
        $data['heslo'] = $heslo;
    }

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