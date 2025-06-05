<?php
// admin/includes/ajax_base.php - Základní zabezpečení pro všechny AJAX akce

session_start();

// Kontrola, zda je požadavek AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    http_response_code(403);
    exit(json_encode(['error' => 'Neplatný požadavek.']));
}

// Kontrola přihlášení
if (!isset($_SESSION['blkt_prihlasen']) || $_SESSION['blkt_prihlasen'] !== true) {
    http_response_code(401);
    exit(json_encode(['error' => 'Neautorizovaný přístup.']));
}

// Kontrola admin oprávnění
if (!isset($_SESSION['blkt_uzivatel_admin']) || $_SESSION['blkt_uzivatel_admin'] != 1) {
    http_response_code(403);
    exit(json_encode(['error' => 'Nedostatečná oprávnění.']));
}

// CSRF kontrola pro POST požadavky
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403);
        exit(json_encode(['error' => 'Neplatný bezpečnostní token.']));
    }
}

// Rate limiting - maximálně 60 požadavků za minutu
$rateLimitKey = 'rate_limit_' . $_SESSION['blkt_uzivatel_id'];
$currentTime = time();
if (!isset($_SESSION[$rateLimitKey])) {
    $_SESSION[$rateLimitKey] = ['count' => 0, 'reset_time' => $currentTime + 60];
}

if ($_SESSION[$rateLimitKey]['reset_time'] < $currentTime) {
    $_SESSION[$rateLimitKey] = ['count' => 0, 'reset_time' => $currentTime + 60];
}

$_SESSION[$rateLimitKey]['count']++;

if ($_SESSION[$rateLimitKey]['count'] > 60) {
    http_response_code(429);
    exit(json_encode(['error' => 'Příliš mnoho požadavků. Zkuste to později.']));
}

// Logování AJAX akcí
function logAjaxAction($action, $data = []) {
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user_id' => $_SESSION['blkt_uzivatel_id'],
        'user_email' => $_SESSION['blkt_uzivatel_mail'],
        'action' => $action,
        'ip' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'data' => $data
    ];
    error_log('[AJAX ACTION] ' . json_encode($logEntry));
}

// Standardní JSON hlavička
header('Content-Type: application/json; charset=utf-8');

// Příklad použití v admin/action/*.php souborech:
// require_once __DIR__ . '/../includes/ajax_base.php';
// logAjaxAction('add_user', ['email' => $email]);