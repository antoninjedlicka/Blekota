<?php
// logout.php

session_start();

// Zničíme všechny session proměnné
$_SESSION = [];

// Ukončíme session na serveru
session_destroy();

// Přesměrování na přihlašovací stránku
header('Location: login.php');
exit;
?>
