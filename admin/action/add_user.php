<?php
/* admin/action/add_user.php */
header('Content-Type: application/json');
require_once __DIR__ . '/../databaze.php';

try {
    // Načíst a validovat vstupy
    $jmeno    = trim($_POST['blkt_jmeno'] ?? '');
    $prijmeni = trim($_POST['blkt_prijmeni'] ?? '');
    $mail     = trim($_POST['blkt_mail'] ?? '');
    $heslo    = $_POST['blkt_heslo'] ?? '';
    $admin    = isset($_POST['blkt_admin']) ? (int) $_POST['blkt_admin'] : 0;
    $idskupiny = !empty($_POST['blkt_idskupiny']) ? (int)$_POST['blkt_idskupiny'] : null;

    if (!$jmeno || !$prijmeni || !$mail) {
        throw new Exception('Všechna pole kromě hesla jsou povinná.');
    }
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Neplatný formát e-mailu.');
    }

    // Pokud není zadané heslo, vygenerujeme náhodné
    if (empty($heslo)) {
        $heslo = bin2hex(random_bytes(8));
    }

    // Sestavit data pro CRUD
    $data = [
        'jmeno'      => $jmeno,
        'prijmeni'   => $prijmeni,
        'mail'       => $mail,
        'heslo'      => $heslo,
        'stav'       => 1,
        'admin'      => $admin,
        'idskupiny'  => $idskupiny,
    ];
    $newId = blkt_insert_uzivatel($data);

    echo json_encode([
        'status' => 'ok',
        'message' => "Uživatel (#{$newId}) byl úspěšně přidán.",
        'generated_password' => empty($_POST['blkt_heslo']) ? $heslo : null
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
    exit;
}