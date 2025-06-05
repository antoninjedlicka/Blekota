<?php
// admin/refresh_csrf.php - Obnova CSRF tokenu
session_start();

// Kontrola AJAX požadavku
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    http_response_code(403);
    exit;
}

// Kontrola přihlášení
if (!isset($_SESSION['blkt_prihlasen']) || $_SESSION['blkt_prihlasen'] !== true) {
    http_response_code(401);
    exit;
}

// Generování nového CSRF tokenu
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'token' => $_SESSION['csrf_token']
]);
?>