<?php
// logout.php

// Načtení centrální session konfigurace
require_once __DIR__ . '/includes/session.php';

// Zničíme všechny session proměnné
$_SESSION = [];

// Smažeme session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Ukončíme session na serveru
session_destroy();

// Přesměrování na login
header('Location: /login.php');
exit;
?>