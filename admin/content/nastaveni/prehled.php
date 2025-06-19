<?php
// admin/content/nastaveni/prehled.php
// Načtení hodnot z databáze podle kódu

require_once __DIR__ . '/../../databaze.php';
$pdo = blkt_db_connect();

// Načteme hodnoty z konfigurace
$nastaveni = [];
$kody = ['WWW', 'BLOG', 'THEME'];

foreach ($kody as $kod) {
    $stmt = $pdo->prepare("SELECT blkt_hodnota FROM blkt_konfigurace WHERE blkt_kod = :kod");
    $stmt->execute([':kod' => $kod]);
    $hodnota = $stmt->fetchColumn();
    $nastaveni[$kod] = $hodnota ?: '';
}

// Pokud není nastavená barva, použijeme výchozí modrou
if (empty($nastaveni['THEME'])) {
    $nastaveni['THEME'] = '#3498db';
}

// Přednastavené barvy
$blkt_prednastavene_barvy = [
    '#3498db' => 'Modrá (výchozí)',
    '#e74c3c' => 'Červená',
    '#27ae60' => 'Zelená',
    '#f39c12' => 'Oranžová',
    '#9b59b6' => 'Fialová',
    '#1abc9c' => 'Tyrkysová',
    '#34495e' => 'Tmavě šedá',
    '#e67e22' => 'Mrkvová',
    '#16a085' => 'Zelený smaragd',
    '#2980b9' => 'Tmavě modrá'
];

// Zjistíme, jestli je aktuální barva mezi přednastaveným
$je_vlastni_barva = !array_key_exists($nastaveni['THEME'], $blkt_prednastavene_barvy);
?>

<form id="blkt-form-nastaveni" action="action/save_nastaveni.php" method="post" class="nastaveni-form">
    <div class="blkt-admin-box">
        <h2>Nastavení webu</h2>

        <div class="blkt-formular-skupina">
            <input type="text" name="WWW" value="<?php echo htmlspecialchars($nastaveni['WWW']); ?>" placeholder=" " required>
            <label for="WWW">Webová adresa</label>
        </div>

        <div class="blkt-formular-skupina">
            <input type="text" name="BLOG" value="<?php echo htmlspecialchars($nastaveni['BLOG']); ?>" placeholder=" " required>
            <label for="BLOG">Adresa blogu</label>
        </div>

        <div class="blkt-formular-skupina blkt-barva-skupina">
            <div class="blkt-barva-container">
                <!-- Levá polovina - výběr přednastavených barev -->
                <div class="blkt-barva-select-wrapper">
                    <select name="THEME_SELECT" id="blkt-theme-select" required>
                        <?php foreach ($blkt_prednastavene_barvy as $hex => $nazev): ?>
                            <option value="<?= $hex ?>"
                                    data-color="<?= $hex ?>"
                                <?= (!$je_vlastni_barva && $nastaveni['THEME'] === $hex) ? 'selected' : '' ?>>
                                <?= $nazev ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="custom" <?= $je_vlastni_barva ? 'selected' : '' ?>>Vlastní barva</option>
                    </select>
                    <label for="blkt-theme-select">Barevné schéma</label>
                </div>

                <!-- Pravá polovina - hex input a tlačítko -->
                <div class="blkt-barva-input-wrapper">
                    <input type="text"
                           id="blkt-color-hex-input"
                           name="THEME"
                           value="<?= htmlspecialchars($nastaveni['THEME']) ?>"
                           placeholder="#000000"
                           maxlength="7"
                           pattern="^#[0-9A-Fa-f]{6}$"
                           required>

                    <button type="button"
                            id="blkt-color-picker-btn"
                            class="blkt-color-picker-btn"
                            title="Vybrat barvu z palety"
                            style="background-color: <?= htmlspecialchars($nastaveni['THEME']) ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 2a10 10 0 0 0 0 20 5 5 0 0 0 0-20"></path>
                            <circle cx="12" cy="8" r="2"></circle>
                            <circle cx="8.5" cy="12.5" r="2"></circle>
                            <circle cx="15.5" cy="12.5" r="2"></circle>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Barevná paleta (mimo formulář) -->
<div id="blkt-color-palette" class="blkt-color-palette" style="display: none;">
    <div class="blkt-palette-header">
        <h4>Vyberte barvu</h4>
        <button type="button" class="blkt-palette-close">&times;</button>
    </div>

    <!-- Přednastavené barvy -->
    <div class="blkt-palette-presets">
        <?php foreach ($blkt_prednastavene_barvy as $hex => $nazev): ?>
            <button type="button"
                    class="blkt-color-preset"
                    data-color="<?= $hex ?>"
                    style="background-color: <?= $hex ?>"
                    title="<?= $nazev ?>">
            </button>
        <?php endforeach; ?>
    </div>

    <!-- HTML5 color picker -->
    <div class="blkt-palette-custom">
        <label>Vlastní barva:</label>
        <input type="color" id="blkt-html-color-picker" value="<?= htmlspecialchars($nastaveni['THEME']) ?>">
    </div>

    <!-- Náhled -->
    <div class="blkt-palette-preview">
        <div class="blkt-preview-box" id="blkt-color-preview-box" style="background-color: <?= htmlspecialchars($nastaveni['THEME']) ?>">
            <p>Náhled vybrané barvy</p>
        </div>
    </div>

    <div class="blkt-palette-actions">
        <button type="button" class="btn btn-cancel" id="blkt-palette-cancel">Zrušit</button>
        <button type="button" class="btn btn-save" id="blkt-palette-apply">Použít</button>
    </div>
</div>