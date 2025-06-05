<?php
// admin/onboarding.php
session_start();

// Kontrola přihlášení
if (!isset($_SESSION['blkt_prihlasen']) || $_SESSION['blkt_prihlasen'] !== true) {
    header('Location: ../login.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- DŮLEŽITÉ! -->
  <title>Vítejte!</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/onboarding.css">
  <script defer src="../js/onboarding.js"></script>
</head>

<body>

<div class="onboarding-container">
  <div id="messages">
    <div class="message">Web je nainstalován!</div>
    <div class="messageB">Zbývá doladit vzhled a fungování webu!</div>
    <div class="messageB">Tak co, dáme se do toho hned?</div>
  </div>

  <button id="go-admin" class="btn">Rovnou do administrace</button>
</div>

</body>
</html>
