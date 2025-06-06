<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Načtení centrální session konfigurace
require_once __DIR__ . '/../includes/session.php';

// Načtení auth funkcí
require_once __DIR__ . '/../includes/auth.php';

// Kontrola přihlášení a admin oprávnění
blkt_vyzaduj_prihlaseni();
blkt_vyzaduj_admina();

// Získání informací o uživateli
$uzivatel = blkt_uzivatel_info();
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrace</title>
    <!-- Preconnect pro fonty -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Fonty -->
    <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:ital,wght@0,100;0,300;0,400;0,500;0,600;0,700;1,100;1,300;1,400;1,500;1,600;1,700&display=swap"
          rel="stylesheet">
    <!-- CSS a JS přesunuto do složek admin/css a admin/js -->
    <link rel="stylesheet" href="css/admin.css">
    <script defer src="js/admin.js"></script>
    <!-- TinyMCE (načte se, až je potřeba ve stránce příspěvků) -->
    <script defer src="../editor/tinymce.min.js"></script>
</head>
<body>
<header class="blkt-hlavicka">
    <?php include __DIR__ . '/includes/header.php'; ?>
</header>
<div class="admin-wrapper">
    <aside class="admin-menu">
        <div class="menu-item active" data-section="dashboard">
            <img src="/media/icons/dashboard.svg" alt="Dashboard">
            <span>Dashboard</span>
        </div>
        <div class="menu-item" data-section="uzivatele">
            <img src="/media/icons/users.svg" alt="Uživatelé">
            <span>Uživatelé</span>
        </div>
        <div class="menu-item" data-section="nastaveni">
            <img src="/media/icons/settings.svg" alt="Nastavení">
            <span>Nastavení</span>
        </div>
        <div class="menu-item" data-section="homepage">
            <img src="/media/icons/home.svg" alt="Homepage">
            <span>Homepage</span>
        </div>
        <div class="menu-item" data-section="seo">
            <img src="/media/icons/seo.svg" alt="SEO">
            <span>SEO</span>
        </div>
        <div class="menu-item" data-section="prispevky">
            <img src="/media/icons/post.svg" alt="Příspěvky">
            <span>Příspěvky</span>
        </div>
        <div class="menu-item" data-section="zivotopis">
            <img src="/media/icons/zivotopis.svg" alt="Životopis">
            <span>Životopis</span>
        </div>
        <div class="menu-item" data-section="obrazky">
            <img src="/media/icons/images.svg" alt="Média">
            <span>Média</span>
        </div>
    </aside>

    <main class="admin-content">
        <section id="admin-section" class="admin-section">
            <!-- Sem se AJAXem vloží celý obsah sekce (včetně vlastních záložek) -->
            <p>Načítám obsah…</p>
        </section>
    </main>
</div>
<footer class="blkt-paticka">
    <?php include __DIR__ . '/includes/footer.php'; ?>
</footer>
<?php include __DIR__ . '/../includes/loader.php'; ?>
</body>
</html>