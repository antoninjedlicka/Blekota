<?php
// install/db_config.php
// Uložení základní konfigurace webu

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Přístup zamítnut.']);
    exit;
}

// Načteme funkce pro práci s databází
require_once __DIR__ . '/../databaze.php';

// Získání dat
$www   = trim($_POST['www'] ?? '');
$blog  = trim($_POST['blog'] ?? '');
$theme = trim($_POST['theme'] ?? '');

if (!$www || !$blog || !$theme) {
    echo json_encode(['error' => 'Vyplňte prosím všechna pole.']);
    exit;
}

try {
    blkt_insert_konfigurace(['nazev' => 'Webová adresa webu', 'kod' => 'WWW', 'hodnota' => $www]);
    blkt_insert_konfigurace(['nazev' => 'Webová adresa blogu', 'kod' => 'BLOG', 'hodnota' => $blog]);
    blkt_insert_konfigurace(['nazev' => 'Téma webu', 'kod' => 'THEME', 'hodnota' => $theme]);
    blkt_insert_konfigurace(['nazev' => 'Nový web', 'kod' => 'NEW', 'hodnota' => 'true']);

    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Chyba při ukládání konfigurace: ' . $e->getMessage()]);
}
exit;
?>
