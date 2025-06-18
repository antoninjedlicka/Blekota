<?php
// admin/action/get_theme_color.php
// Vrací aktuální barvu tématu z databáze

header('Content-Type: application/json');

require_once __DIR__ . '/../databaze.php';

try {
    $pdo = blkt_db_connect();

    // Načteme barvu z konfigurace
    $stmt = $pdo->prepare("SELECT blkt_hodnota FROM blkt_konfigurace WHERE blkt_kod = 'THEME'");
    $stmt->execute();
    $color = $stmt->fetchColumn();

    // Pokud není nastavena, použijeme výchozí modrou
    if (!$color) {
        $color = '#3498db';
    }

    echo json_encode([
        'status' => 'ok',
        'color' => $color
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'error' => $e->getMessage()
    ]);
}