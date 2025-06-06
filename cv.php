<?php
// cv.php - dynamická verze načítající data z databáze
require_once __DIR__ . '/databaze.php';

$pdo = blkt_db_connect();

// Načtení základních údajů
$zakladni = [];
$konfig_klice = ['cv_jmeno', 'cv_lokace', 'cv_telefon', 'cv_email', 'cv_foto'];
foreach ($konfig_klice as $klic) {
    $stmt = $pdo->prepare("SELECT blkt_hodnota FROM blkt_konfigurace WHERE blkt_kod = :kod");
    $stmt->execute([':kod' => $klic]);
    $zakladni[$klic] = $stmt->fetchColumn() ?: '';
}

// Načtení všech položek životopisu
$polozky = blkt_get_zivotopis_polozky('vse');

// Rozdělení podle typu
$data = [
    'profese' => [],
    'dovednosti' => [],
    'vlastnosti' => [],
    'jazyky' => [],
    'vzdelani' => [],
    'zajmy' => []
];

foreach ($polozky as $polozka) {
    $typ = $polozka['blkt_typ'];
    if ($typ === 'dovednost') $typ = 'dovednosti';
    if ($typ === 'vlastnost') $typ = 'vlastnosti';
    if ($typ === 'jazyk') $typ = 'jazyky';
    if ($typ === 'zajem') $typ = 'zajmy';

    if (isset($data[$typ])) {
        $data[$typ][] = $polozka;
    }
}

$pageTitle = ($zakladni['cv_jmeno'] ?: 'Životopis') . ' - CV';
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:ital,wght@0,100;0,300;0,400;0,500;0,600;0,700;1,100;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/cv.css">
</head>
<body>

<header class="blkt-hlavicka">
    <?php include __DIR__ . '/includes/header.php'; ?>
</header>

<main class="blkt-obsah-stranky">
    <article class="blkt-cv">
        <!-- HLAVIČKA CV -->
        <div class="blkt-cv-hlavicka">
            <?php if ($zakladni['cv_foto']): ?>
                <img src="<?= htmlspecialchars($zakladni['cv_foto']) ?>" alt="<?= htmlspecialchars($zakladni['cv_jmeno']) ?>" class="blkt-cv-avatar">
            <?php endif; ?>

            <div class="blkt-cv-info">
                <h1><?= htmlspecialchars($zakladni['cv_jmeno']) ?></h1>
                <ul class="blkt-cv-kontakt">
                    <?php if ($zakladni['cv_lokace']): ?>
                        <li>
                            <span class="ikona">📍</span>
                            <span><?= htmlspecialchars($zakladni['cv_lokace']) ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if ($zakladni['cv_telefon']): ?>
                        <li>
                            <span class="ikona">📞</span>
                            <span><?= htmlspecialchars($zakladni['cv_telefon']) ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if ($zakladni['cv_email']): ?>
                        <li>
                            <span class="ikona">✉️</span>
                            <span><?= htmlspecialchars($zakladni['cv_email']) ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- TĚLO CV -->
        <div class="blkt-cv-telo">
            <!-- PROFESNÍ ZKUŠENOSTI -->
            <?php if (!empty($data['profese'])): ?>
                <section class="blkt-cv-sekce">
                    <h2>Profesní zkušenosti</h2>
                    <?php foreach ($data['profese'] as $pozice): ?>
                        <div class="blkt-cv-pozice">
                            <h3><?= htmlspecialchars($pozice['blkt_nazev']) ?></h3>
                            <div class="blkt-cv-firma"><?= htmlspecialchars($pozice['blkt_podnazev']) ?></div>
                            <div class="blkt-cv-datum">
                                <?= htmlspecialchars($pozice['blkt_datum_od']) ?>
                                <?php if ($pozice['blkt_datum_do']): ?>
                                    – <?= htmlspecialchars($pozice['blkt_datum_do']) ?>
                                <?php endif; ?>
                            </div>
                            <?php if ($pozice['blkt_popis']): ?>
                                <div class="blkt-cv-popis"><?= htmlspecialchars($pozice['blkt_popis']) ?></div>
                            <?php endif; ?>
                            <?php if ($pozice['blkt_obsah']): ?>
                                <div class="blkt-cv-obsah"><?= $pozice['blkt_obsah'] ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>

            <!-- DOVEDNOSTI -->
            <?php if (!empty($data['dovednosti'])): ?>
                <section class="blkt-cv-sekce">
                    <h2>Dovednosti a technologie</h2>
                    <div class="blkt-cv-dovednosti">
                        <?php foreach ($data['dovednosti'] as $kategorie): ?>
                            <div class="blkt-cv-dovednost-skupina">
                                <h4><?= htmlspecialchars($kategorie['blkt_podnazev']) ?></h4>
                                <div class="blkt-cv-tags">
                                    <?php
                                    $tagy = array_map('trim', explode(',', $kategorie['blkt_tagy']));
                                    foreach ($tagy as $tag):
                                        ?>
                                        <span class="blkt-cv-tag"><?= htmlspecialchars($tag) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- VLASTNOSTI -->
            <?php if (!empty($data['vlastnosti'])): ?>
                <section class="blkt-cv-sekce">
                    <h2>Vlastnosti</h2>
                    <div class="blkt-cv-vlastnosti">
                        <?php foreach ($data['vlastnosti'] as $vlastnost): ?>
                            <div class="blkt-cv-vlastnost">
                                <h4>
                                    <?php if ($vlastnost['blkt_ikona']): ?>
                                        <?= htmlspecialchars($vlastnost['blkt_ikona']) ?>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($vlastnost['blkt_nazev']) ?>
                                </h4>
                                <?php if ($vlastnost['blkt_popis']): ?>
                                    <p><?= htmlspecialchars($vlastnost['blkt_popis']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- JAZYKY -->
            <?php if (!empty($data['jazyky'])): ?>
                <section class="blkt-cv-sekce">
                    <h2>Jazyky</h2>
                    <div class="blkt-cv-dovednosti">
                        <?php foreach ($data['jazyky'] as $jazyk): ?>
                            <div class="blkt-cv-dovednost-skupina">
                                <h4>
                                    <?php if ($jazyk['blkt_ikona']): ?>
                                        <?= htmlspecialchars($jazyk['blkt_ikona']) ?>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($jazyk['blkt_nazev']) ?>
                                </h4>
                                <p><?= htmlspecialchars($jazyk['blkt_uroven']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- VZDĚLÁNÍ A ZÁJMY -->
            <?php if (!empty($data['vzdelani']) || !empty($data['zajmy'])): ?>
                <section class="blkt-cv-sekce">
                    <h2>Vzdělání a zájmy</h2>

                    <?php foreach ($data['vzdelani'] as $skola): ?>
                        <div class="blkt-cv-pozice">
                            <h3><?= htmlspecialchars($skola['blkt_nazev']) ?></h3>
                            <div class="blkt-cv-datum">
                                <?= htmlspecialchars($skola['blkt_datum_od']) ?>
                                <?php if ($skola['blkt_datum_do']): ?>
                                    – <?= htmlspecialchars($skola['blkt_datum_do']) ?>
                                <?php endif; ?>
                            </div>
                            <?php if ($skola['blkt_popis']): ?>
                                <div class="blkt-cv-popis"><?= htmlspecialchars($skola['blkt_popis']) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <?php if (!empty($data['zajmy'])): ?>
                        <div class="blkt-cv-pozice">
                            <h3>Zájmy</h3>
                            <div class="blkt-cv-tags">
                                <?php
                                $tagy = array_map('trim', explode(',', $data['zajmy'][0]['blkt_tagy']));
                                foreach ($tagy as $tag):
                                    ?>
                                    <span class="blkt-cv-tag"><?= htmlspecialchars($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </div>
    </article>
</main>

<footer class="blkt-paticka">
    <?php include __DIR__ . '/includes/footer.php'; ?>
</footer>

<?php include __DIR__ . '/includes/loader.php'; ?>
<script src="/js/cv.js"></script>

</body>
</html>