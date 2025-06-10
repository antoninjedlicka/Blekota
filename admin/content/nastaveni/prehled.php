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

        <div class="blkt-formular-skupina">
            <select name="THEME" required>
                <option value="#ff0000" <?php echo $nastaveni['THEME'] === '#ff0000' ? 'selected' : ''; ?>>Červená</option>
                <option value="#0000ff" <?php echo $nastaveni['THEME'] === '#0000ff' ? 'selected' : ''; ?>>Modrá</option>
                <option value="#00ff00" <?php echo $nastaveni['THEME'] === '#00ff00' ? 'selected' : ''; ?>>Zelená</option>
            </select>
            <label for="THEME">Barevné schéma</label>
        </div>
    </div>
</form>