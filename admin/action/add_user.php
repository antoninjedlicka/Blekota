<?php
/* admin/action/add_user.php */
// Přidá nového uživatele do tabulky blkt_uzivatele přes existující CRUD funkci
header('Content-Type: application/json');
require_once __DIR__ . '/../databaze.php';

try {
    // Načíst a validovat vstupy
    $jmeno    = trim($_POST['blkt_jmeno'] ?? '');
    $prijmeni = trim($_POST['blkt_prijmeni'] ?? '');
    $mail     = trim($_POST['blkt_mail'] ?? '');
    $admin    = isset($_POST['blkt_admin']) ? (int) $_POST['blkt_admin'] : 0;

    if (!$jmeno || !$prijmeni || !$mail) {
        throw new Exception('Všechna pole jsou povinná.');
    }
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Neplatný formát e-mailu.');
    }

    // Sestavit data pro CRUD
    $data = [
        'jmeno'    => $jmeno,
        'prijmeni' => $prijmeni,
        'mail'     => $mail,
        'heslo'    => password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT),
        'stav'     => 1,
        'admin'    => $admin,
    ];
    $newId = blkt_insert_uzivatel($data);

    echo json_encode(['status' => 'ok', 'message' => "Uživatel (#{$newId}) byl úspěšně přidán."]);
    exit;

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
    exit;
}