<?php
// admin/action/get_dashboard_stats.php
header('Content-Type: application/json');

require_once __DIR__ . '/../databaze.php';

try {
    $pdo = blkt_db_connect();

    // Počet uživatelů
    $stmt = $pdo->query("SELECT COUNT(*) FROM blkt_uzivatele");
    $users = $stmt->fetchColumn();

    // Počet příspěvků
    $stmt = $pdo->query("SELECT COUNT(*) FROM blkt_prispevky");
    $posts = $stmt->fetchColumn();

    // Aktuální téma
    $stmt = $pdo->prepare("SELECT blkt_hodnota FROM blkt_konfigurace WHERE blkt_kod = 'THEME' LIMIT 1");
    $stmt->execute();
    $theme = $stmt->fetchColumn();

    // Převod barevného kódu na název
    $theme_names = [
        '#ff0000' => 'Červená',
        '#0000ff' => 'Modrá',
        '#00ff00' => 'Zelená'
    ];
    $theme_name = $theme_names[$theme] ?? 'Výchozí';

    echo json_encode([
        'status' => 'ok',
        'users' => $users,
        'posts' => $posts,
        'theme' => $theme_name
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'error' => $e->getMessage()
    ]);
}