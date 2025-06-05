<?php
// debug_session.php - Dočasný soubor pro kontrolu session
require_once __DIR__ . '/includes/session.php';

echo "<h1>Session Debug</h1>";
echo "<h2>Session ID: " . session_id() . "</h2>";
echo "<h2>Session Name: " . session_name() . "</h2>";
echo "<h2>Session Status: " . session_status() . "</h2>";
echo "<h2>Cookie Params:</h2>";
echo "<pre>";
print_r(session_get_cookie_params());
echo "</pre>";
echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
echo "<h2>Cookies:</h2>";
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";
echo "<h2>Server Info:</h2>";
echo "HTTPS: " . (isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'not set') . "<br>";
echo "HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "<br>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
?>