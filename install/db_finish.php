<?php
// install/db_finish.php
// Dokončení instalace – vytvoření instalace.lock

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Přístup zamítnut.']);
    exit;
}

// Vytvoření souboru instalace.lock
$lockFile = __DIR__ . '/../instalace.lock';
$obsah = 'Instalace byla dokončena dne ' . date('Y-m-d H:i:s');

if (file_put_contents($lockFile, $obsah) === false) {
    echo json_encode(['error' => 'Nepodařilo se vytvořit instalační zámek.']);
    exit;
}

// Úspěch
echo json_encode(['status' => 'ok']);
exit;
?>
