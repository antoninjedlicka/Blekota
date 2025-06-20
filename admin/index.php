<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Načtení centrální session konfigurace
require_once __DIR__ . '/../includes/session.php';

// Načtení auth funkcí
require_once __DIR__ . '/../includes/auth.php';


// Kontrola přihlášení (už ne nutně admin)
blkt_vyzaduj_prihlaseni();

// Získání informací o uživateli a jeho oprávnění
$uzivatel = blkt_uzivatel_info();
$povolene_sekce = blkt_ziskej_povolene_sekce();

// Pokud uživatel nemá přístup k žádné sekci, přesměrovat
if (empty($povolene_sekce)) {
    $_SESSION['blkt_chyba_opravneni'] = 'Nemáte oprávnění k žádné administrační sekci.';
    header('Location: /');
    exit;
}

// Definice všech sekcí s jejich vlastnostmi
$vsechny_sekce = [
    'dashboard' => [
        'nazev' => 'Dashboard',
        'ikona' => '/media/icons/dashboard.svg',
        'alt' => 'Dashboard'
    ],
    'uzivatele' => [
        'nazev' => 'Uživatelé',
        'ikona' => '/media/icons/users.svg',
        'alt' => 'Uživatelé'
    ],
    'nastaveni' => [
        'nazev' => 'Nastavení',
        'ikona' => '/media/icons/settings.svg',
        'alt' => 'Nastavení'
    ],
    'homepage' => [
        'nazev' => 'Homepage',
        'ikona' => '/media/icons/home.svg',
        'alt' => 'Homepage'
    ],
    'seo' => [
        'nazev' => 'SEO',
        'ikona' => '/media/icons/seo.svg',
        'alt' => 'SEO'
    ],
    'prispevky' => [
        'nazev' => 'Příspěvky',
        'ikona' => '/media/icons/post.svg',
        'alt' => 'Příspěvky'
    ],
    'zivotopis' => [
        'nazev' => 'Životopis',
        'ikona' => '/media/icons/zivotopis.svg',
        'alt' => 'Životopis'
    ],
    'obrazky' => [
        'nazev' => 'Média',
        'ikona' => '/media/icons/images.svg',
        'alt' => 'Média'
    ]
];

// Získat první povolenou sekci jako výchozí
$prvni_povolena_sekce = 'dashboard';
foreach ($vsechny_sekce as $sekce_id => $sekce_info) {
    if (in_array($sekce_id, $povolene_sekce) || in_array('all', $povolene_sekce)) {
        $prvni_povolena_sekce = $sekce_id;
        break;
    }
}
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
    <link rel="stylesheet" href="css/modal.css">
    <script defer src="js/admin.js"></script>
    <!-- TinyMCE (načte se, až je potřeba ve stránce příspěvků) -->
    <script defer src="../editor/tinymce.min.js"></script>
    <!-- Modal helpers -->
    <script defer src="js/modal-helpers.js"></script>

    <script>
        // Předat první povolenou sekci do JavaScriptu
        window.prvniPovolenaSekce = '<?= $prvni_povolena_sekce ?>';
    </script>
</head>
<body>
<header class="blkt-hlavicka">
    <?php include __DIR__ . '/includes/header.php'; ?>
</header>
<div class="admin-wrapper">
    <aside class="admin-menu">
        <!-- Horní část - položky menu -->
        <div class="menu-top-section">
            <?php foreach ($vsechny_sekce as $sekce_id => $sekce_info): ?>
                <?php if (in_array($sekce_id, $povolene_sekce) || in_array('all', $povolene_sekce)): ?>
                    <div class="menu-item <?= $sekce_id === $prvni_povolena_sekce ? 'active' : '' ?>" data-section="<?= $sekce_id ?>">
                        <img src="<?= htmlspecialchars($sekce_info['ikona']) ?>" alt="<?= htmlspecialchars($sekce_info['alt']) ?>">
                        <span><?= htmlspecialchars($sekce_info['nazev']) ?></span>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Spodní část - user info + logout -->
        <div class="menu-bottom-section">
            <div class="menu-divider"></div>

            <!-- Informace o uživateli -->
            <div class="menu-user-info">
                <div class="user-name">
                    <?= htmlspecialchars($uzivatel['jmeno'] . ' ' . $uzivatel['prijmeni']) ?>
                </div>
                <?php if ($uzivatel['admin']): ?>
                    <span class="admin-badge">Admin</span>
                <?php else: ?>
                    <?php
                    $opravneni = blkt_ziskej_opravneni_uzivatele();
                    if (!empty($opravneni['skupina'])): ?>
                        <span class="group-badge"><?= htmlspecialchars($opravneni['skupina']) ?></span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <a href="/logout.php" class="menu-logout">
                <img src="/media/icons/logout.svg" alt="Odhlásit">
                <span>Odhlásit</span>
            </a>
        </div>
    </aside>

    <main class="admin-content">
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

        <?php if ($chyba = blkt_chyba_opravneni()): ?>
            <div class="blkt-chyba-opravneni">
                <?= htmlspecialchars($chyba) ?>
            </div>
        <?php endif; ?>

        <section id="admin-section" class="admin-section">
            <!-- Sem se AJAXem vloží celý obsah sekce (včetně vlastních záložek) -->
            <p>Načítám obsah…</p>
        </section>
    </main>
</div>
<footer class="blkt-paticka">
    <?php include __DIR__ . '/includes/footer.php'; ?>
</footer>
</body>
</html>