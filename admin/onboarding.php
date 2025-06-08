<?php
// admin/onboarding.php
require_once __DIR__ . '/../includes/session.php';

// Kontrola přihlášení
if (!isset($_SESSION['blkt_prihlasen']) || $_SESSION['blkt_prihlasen'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Při dokončení onboardingu odstraníme příznak "nový web"
if (isset($_GET['dokoncit'])) {
    require_once __DIR__ . '/../databaze.php';

    // Nastavíme NEW na false
    $pdo = blkt_db_connect();
    $stmt = $pdo->prepare("UPDATE blkt_konfigurace SET blkt_hodnota = 'false' WHERE blkt_kod = 'NEW'");
    $stmt->execute();

    // Přesměrujeme na homepage
    header('Location: /');
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vítejte!</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/onboarding.css">
    <script defer src="js/onboarding.js"></script>
</head>

<body>

<div class="onboarding-container">
    <div id="messages">
        <div class="message">Web je nainstalován!</div>
        <div class="messageB">Zbývá doladit vzhled a fungování webu!</div>
        <div class="messageB">Tak co, dáme se do toho hned?</div>
    </div>

    <button id="go-admin" class="btn">Rovnou do administrace</button>
    <div style="text-align: center; margin-top: 1rem;">
        <a href="?dokoncit=1" style="color: #666; font-size: 0.9em;">Přeskočit a jít na web</a>
    </div>
</div>

</body>
</html>