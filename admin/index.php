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
        <!-- Přidej tuto strukturu do admin/index.php přímo do <main class="admin-content"> jako první element -->

        <!-- Admin Loader -->
        <div id="blkt-admin-loader" class="blkt-admin-loader">
            <div class="blkt-loader-content">
                <div class="blkt-loader-logo">
                    <svg width="120" height="120" viewBox="0 0 120 120">
                        <defs>
                            <linearGradient id="loader-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#3498db;stop-opacity:1">
                                    <animate attributeName="stop-color" values="#3498db;#5dade2;#3498db" dur="3s" repeatCount="indefinite" />
                                </stop>
                                <stop offset="100%" style="stop-color:#5dade2;stop-opacity:1">
                                    <animate attributeName="stop-color" values="#5dade2;#3498db;#5dade2" dur="3s" repeatCount="indefinite" />
                                </stop>
                            </linearGradient>
                            <filter id="loader-glow">
                                <feGaussianBlur stdDeviation="3" result="coloredBlur"/>
                                <feMerge>
                                    <feMergeNode in="coloredBlur"/>
                                    <feMergeNode in="SourceGraphic"/>
                                </feMerge>
                            </filter>
                        </defs>

                        <!-- Vnější rotující kruh -->
                        <circle cx="60" cy="60" r="50" fill="none" stroke="url(#loader-gradient)" stroke-width="2" opacity="0.3"/>

                        <!-- Tři rotující kruhy -->
                        <g id="rotating-circles">
                            <circle cx="60" cy="20" r="8" fill="url(#loader-gradient)" filter="url(#loader-glow)">
                                <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" repeatCount="indefinite" />
                            </circle>
                            <circle cx="95" cy="60" r="8" fill="url(#loader-gradient)" filter="url(#loader-glow)">
                                <animate attributeName="opacity" values="0.3;1;0.3" dur="1.5s" repeatCount="indefinite" />
                            </circle>
                            <circle cx="25" cy="60" r="8" fill="url(#loader-gradient)" filter="url(#loader-glow)">
                                <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" repeatCount="indefinite" />
                            </circle>
                            <animateTransform
                                    attributeName="transform"
                                    attributeType="XML"
                                    type="rotate"
                                    from="0 60 60"
                                    to="360 60 60"
                                    dur="2s"
                                    repeatCount="indefinite"/>
                        </g>

                        <!-- Střední pulzující kruh -->
                        <circle cx="60" cy="60" r="25" fill="none" stroke="url(#loader-gradient)" stroke-width="3" filter="url(#loader-glow)">
                            <animate attributeName="r" values="25;30;25" dur="1s" repeatCount="indefinite" />
                            <animate attributeName="opacity" values="0.5;1;0.5" dur="1s" repeatCount="indefinite" />
                        </circle>

                        <!-- Logo text -->
                        <text x="60" y="65" text-anchor="middle" font-family="'Signika Negative', sans-serif" font-size="16" font-weight="600" fill="url(#loader-gradient)">
                            BLKT
                        </text>
                    </svg>
                </div>
                <div class="blkt-loader-text">
                    <span class="blkt-loading-text">Načítám</span>
                    <span class="blkt-loading-dots">
                <span>.</span>
                <span>.</span>
                <span>.</span>
            </span>
                </div>
                <div class="blkt-loader-progress">
                    <div class="blkt-loader-progress-bar"></div>
                </div>
            </div>
        </div>
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