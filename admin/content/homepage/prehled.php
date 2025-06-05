<?php
// admin/homepage/prehled.php

$homepage_klice = [
    'homepage_omne'     => 'Text „O mně“',
    'homepage_uvod'     => 'Uvítací texty',
    'homepage_galerie'  => 'Obrázky galerie'
];

$homepage_data = [];
foreach ($homepage_klice as $kod => $popis) {
    $stmt = blkt_db_connect()->prepare("SELECT blkt_hodnota FROM blkt_konfigurace WHERE blkt_kod = :kod");
    $stmt->execute([':kod' => $kod]);
    $homepage_data[$kod] = $stmt->fetchColumn() ?: '';
}

$uvodni_texty = json_decode($homepage_data['homepage_uvod'], true) ?? [];
$obrazky = json_decode($homepage_data['homepage_galerie'], true) ?? [];
?>

<form id="blkt-form-homepage" action="action/edit_homepage.php" method="post" class="nastaveni-form">

    <!-- BOX: O mně -->
    <div class="blkt-admin-box">
        <h2>Povězte něco o sobě</h2>
        <div class="blkt-formular-skupina">
            <textarea name="homepage_omne" rows="6" placeholder="Zadej text o sobě"><?php echo htmlspecialchars($homepage_data['homepage_omne']); ?></textarea>
            <label for="homepage_omne">Krátký text o Vás</label>
        </div>
    </div>

    <!-- BOX: Galerie -->
    <div class="blkt-admin-box">
        <h2>Zvolte prezentované obrázky</h2>
        <p>Vybrat jichh můžete maximálně 5.</p>
        <div id="blkt-galerie-vybrane">
            <?php foreach ($obrazky as $src): ?>
                <div class="blkt-galerie-obrazek">
                    <input type="hidden" name="homepage_galerie[]" value="<?php echo htmlspecialchars($src); ?>">
                    <img src="<?php echo htmlspecialchars($src); ?>" class="galerie-nahled" data-src="<?php echo htmlspecialchars($src); ?>">
                    <button type="button" class="blkt-galerie-odebrat">&times;</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="blkt-vybrat-obrazky">Vybrat z galerie</button>
    </div>

    <!-- BOX: Uvítací texty -->
    <div class="blkt-admin-box">
        <h2>Uvítací texty</h2>
        <ul class="blkt-dynamicke-boxy" id="blkt-uvitaci-texty">
            <?php foreach ($uvodni_texty as $text): ?>
                <li>
                    <input type="text" name="homepage_uvod[]" value="<?php echo htmlspecialchars($text); ?>">
                    <button type="button" class="blkt-odebrat-radek">✕</button>
                </li>
            <?php endforeach; ?>
        </ul>
        <button type="button" id="blkt-pridat-uvitani">Přidat text</button>
    </div>
</form>

<!-- MODAL Galerie -->
<div id="blkt-gallery-overlay" class="blkt-modal-overlay" style="display:none;"></div>
<div id="blkt-gallery-modal" class="blkt-modal" style="display:none;max-width:600px;">
    <div class="blkt-modal-header">
        <h3>Galerie obrázků</h3>
        <button class="blkt-modal-close">&times;</button>
    </div>
    <div class="blkt-modal-body">
        <div class="blkt-gallery-images"></div>
    </div>
    <div class="modal-actions" style="padding:1rem; display:flex; justify-content:flex-end; gap:1rem;">
        <button type="button" id="blkt-gallery-cancel" class="btn btn-cancel">Zrušit</button>
    </div>
</div>
