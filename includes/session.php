<?php
// includes/session.php - Centrální správa session
// Tento soubor musí být includován jako první ve všech PHP souborech

// Kontrola, zda session již neběží
if (session_status() === PHP_SESSION_NONE) {
    // Nastavení session parametrů PŘED session_start()
    ini_set('session.use_cookies', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_lifetime', 0); // Do zavření prohlížeče
    ini_set('session.gc_maxlifetime', 3600); // 1 hodina

    // Nastavení cookie parametrů
    session_set_cookie_params([
        'lifetime' => 0, // Session cookie
        'path' => '/',
        'domain' => '', // Automaticky použije aktuální doménu
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax' // Změna z 'Strict' na 'Lax' pro lepší kompatibilitu
    ]);

    // Nastavení názvu session
    session_name('BLKT_SESSID');

    // Start session
    session_start();
}