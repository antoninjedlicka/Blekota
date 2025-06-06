<?php
// admin/content/zivotopis.php
require_once __DIR__ . '/../databaze.php';

// Načteme základní údaje z konfigurace
$pdo = blkt_db_connect();
$zakladni_udaje = [];
$konfig_klice = ['cv_jmeno', 'cv_lokace', 'cv_telefon', 'cv_email', 'cv_foto'];

foreach ($konfig_klice as $klic) {
    $stmt = $pdo->prepare("SELECT blkt_hodnota FROM blkt_konfigurace WHERE blkt_kod = :kod");
    $stmt->execute([':kod' => $klic]);
    $zakladni_udaje[$klic] = $stmt->fetchColumn() ?: '';
}

// Načteme všechny položky životopisu
$vsechny_polozky = blkt_get_zivotopis_polozky('vse');

// Rozdělíme podle typu
$polozky_podle_typu = [];
foreach ($vsechny_polozky as $polozka) {
    $polozky_podle_typu[$polozka['blkt_typ']][] = $polozka;
}
?>

<nav class="blkt-tabs">
    <button class="active" data-tab="prehled">Přehled</button>
    <button data-tab="zakladni">Základní údaje</button>
    <button data-tab="profese">Profesní zkušenosti</button>
    <button data-tab="dovednosti">Dovednosti</button>
    <button data-tab="vlastnosti">Vlastnosti</button>
    <button data-tab="jazyky">Jazyky</button>
    <button data-tab="vzdelani">Vzdělání a zájmy</button>
</nav>

<!-- WRAPPER PRO SPRÁVNÉ ROZLOŽENÍ -->
<div class="blkt-zivotopis-wrapper">

    <!-- FORMULÁŘ -->
    <form id="blkt-form-zivotopis" method="post" action="action/save_zivotopis.php">

        <div id="tab-prehled" class="tab-content">
            <?php include __DIR__ . '/zivotopis/prehled.php'; ?>
        </div>

        <div id="tab-zakladni" class="tab-content" style="display:none">
            <?php include __DIR__ . '/zivotopis/zakladni.php'; ?>
        </div>

        <div id="tab-profese" class="tab-content" style="display:none">
            <?php include __DIR__ . '/zivotopis/profese.php'; ?>
        </div>

        <div id="tab-dovednosti" class="tab-content" style="display:none">
            <?php include __DIR__ . '/zivotopis/dovednosti.php'; ?>
        </div>

        <div id="tab-vlastnosti" class="tab-content" style="display:none">
            <?php include __DIR__ . '/zivotopis/vlastnosti.php'; ?>
        </div>

        <div id="tab-jazyky" class="tab-content" style="display:none">
            <?php include __DIR__ . '/zivotopis/jazyky.php'; ?>
        </div>

        <div id="tab-vzdelani" class="tab-content" style="display:none">
            <?php include __DIR__ . '/zivotopis/vzdelani.php'; ?>
        </div>

    </form>

    <!-- STICKY SAVE TLAČÍTKO -->
    <div class="blkt-sticky-save">
        <button type="submit" form="blkt-form-zivotopis" class="btn btn-save">Uložit všechny změny</button>
    </div>

</div>

<script defer src="js/zivotopis.js"></script>