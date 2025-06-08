<?php
// homepage.php
require_once 'databaze.php';

// Načti data z konfigurace
function blkt_nacti_konfig($kod, $decode_json = false) {
    $stmt = blkt_db_connect()->prepare("SELECT blkt_hodnota FROM blkt_konfigurace WHERE blkt_kod = :kod LIMIT 1");
    $stmt->execute([':kod' => $kod]);
    $hodnota = $stmt->fetchColumn();
    if ($decode_json) {
        return json_decode($hodnota ?? '', true) ?? [];
    }
    return $hodnota ?? '';
}

// Data
$uvitaci_texty = blkt_nacti_konfig('homepage_uvod', true);
$galerie = blkt_nacti_konfig('homepage_galerie', true);
$omne = blkt_nacti_konfig('homepage_omne');

$pageTitle = 'Vítejte na webu blekota.online';
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>

    <!-- SEO meta -->
    <meta name="description" content="<?php echo htmlspecialchars(blkt_nacti_seo('seo_description')); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars(blkt_nacti_seo('seo_keywords')); ?>">
    <meta name="author" content="<?php echo htmlspecialchars(blkt_nacti_seo('seo_author')); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars(blkt_nacti_seo('WWW')); ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars(blkt_nacti_seo('seo_og_title')); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars(blkt_nacti_seo('seo_og_description')); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars(blkt_nacti_seo('seo_og_image')); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars(blkt_nacti_seo('WWW')); ?>">
    <meta property="og:type" content="<?php echo htmlspecialchars(blkt_nacti_seo('seo_og_type')); ?>">

    <!-- Strukturovaná data -->
    <script type="application/ld+json"><?php echo blkt_nacti_seo('seo_ld_json'); ?></script>


    <!-- CSS a JS -->
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/homepage.css">
    <script defer src="/js/homepage.js"></script>


</head>
<body>

<header class="blkt-hlavicka">
    <?php include __DIR__ . '/includes/header.php'; ?>
</header>

<main class="blkt-homepage-obsah">
    <section class="blkt-homepage-sloupce">
        <!-- LEVÝ SLOUPEC -->
        <div class="blkt-homepage-sloupec">
            <!-- ÚVODNÍ TEXTY -->
            <div id="blkt-kontejner-uvod" class="blkt-kontejner-vstup">
                <div class="blkt-homepage-text-kontejner">
                    <?php if (!empty($uvitaci_texty)): ?>
                        <?php foreach ($uvitaci_texty as $radek): ?>
                            <p class="blkt-homepage-text"><?php echo htmlspecialchars($radek); ?></p>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="blkt-homepage-text">Vítejte na dočasné domovské stránce.</p>
                        <p class="blkt-homepage-text">Ještě nějakou chvilku potrvá, než bude všechno úplně hotovo!</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- GALERIE -->
            <div id="blkt-kontejner-galerie" class="blkt-kontejner-vstup">
                <div class="blkt-slider">
                    <?php if (!empty($galerie)): ?>
                        <?php foreach ($galerie as $src): ?>
                            <img src="<?php echo htmlspecialchars($src); ?>" alt="Obrázek" loading="lazy">
                        <?php endforeach; ?>
                    <?php else: ?>
                        <img src="/home/gallery/img1.png" alt="Ukázkový obrázek 1" loading="lazy">
                        <img src="/home/gallery/img2.png" alt="Ukázkový obrázek 2" loading="lazy">
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- PRAVÝ SLOUPEC -->
        <div class="blkt-homepage-sloupec">
            <!-- O MNĚ -->
            <div id="blkt-kontejner-omne" class="blkt-kontejner-vstup">
                <h2 class="blkt-homepage-nadpis">O mně</h2>
                <img src="/media/autor.png" alt="Autor webu" class="blkt-fotka" loading="lazy">
                <p><?php echo nl2br(htmlspecialchars($omne)); ?></p>

                <?php
                // Kontrola existence záznamů v životopisu
                $stmt = blkt_db_connect()->prepare("SELECT COUNT(*) FROM blkt_zivotopis WHERE blkt_stav = 1");
                $stmt->execute();
                $pocet_zaznamu = $stmt->fetchColumn();

                if ($pocet_zaznamu > 0): ?>
                    <div class="blkt-homepage-tlacitko-kontejner">
                        <a href="/cv" class="blkt-tlacitko blkt-tlacitko-zivotopis">
                            <span class="blkt-ikona-zivotopis">📄</span>
                            Zobrazit životopis
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- POSLEDNÍ PŘÍSPĚVEK -->
            <div id="blkt-kontejner-prispevek" class="blkt-kontejner-vstup">
                <h2 class="blkt-homepage-nadpis">Poslední z blogu</h2>
                <?php include __DIR__ . '/home/posledni_prispevek.php'; ?>
            </div>
        </div>
    </section>
</main>

<footer class="blkt-paticka">
    <?php include __DIR__ . '/includes/footer.php'; ?>
</footer>
<?php include 'includes/loader.php'; ?>
</body>
</html>
