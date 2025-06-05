<?php
// install/db_admin.php
// Vytvoření administrátorského účtu

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Přístup zamítnut.']);
    exit;
}

// Načteme funkce pro práci s databází
require_once __DIR__ . '/../databaze.php';

// Získání dat
$jmeno          = trim($_POST['jmeno'] ?? '');
$prijmeni       = trim($_POST['prijmeni'] ?? '');
$mail           = trim($_POST['mail'] ?? '');
$heslo          = $_POST['heslo'] ?? '';
$heslo_confirm  = $_POST['heslo_confirm'] ?? '';

if (!$jmeno || !$prijmeni || !$mail || !$heslo || !$heslo_confirm) {
    echo json_encode(['error' => 'Vyplňte prosím všechna pole.']);
    exit;
}

if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Neplatný formát e-mailu.']);
    exit;
}

if ($heslo !== $heslo_confirm) {
    echo json_encode(['error' => 'Hesla se neshodují.']);
    exit;
}

try {
    blkt_insert_uzivatel([
        'jmeno'    => $jmeno,
        'prijmeni' => $prijmeni,
        'mail'     => $mail,
        'heslo'    => $heslo,
        'stav'     => 1,
        'admin'    => 1
    ]);

    // AUTO-LOGIN PO ÚSPĚŠNÉM VYTVOŘENÍ
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['blkt_prihlasen'] = true;
    $_SESSION['blkt_uzivatel_jmeno'] = $jmeno;
    $_SESSION['blkt_uzivatel_prijmeni'] = $prijmeni;
    $_SESSION['blkt_uzivatel_mail'] = $mail;
    $_SESSION['blkt_uzivatel_admin'] = 1;

    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Chyba při vytváření administrátora: ' . $e->getMessage()]);
}

exit;
?>
