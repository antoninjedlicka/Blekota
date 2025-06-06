<?php
// admin/content/zivotopis/prehled.php
?>
<div class="dashboard-section">
    <h3>Přehled životopisu</h3>
    <p>Zde můžete spravovat všechny části svého dynamického životopisu.</p>

    <div class="blkt-cv-stats">
        <h4>Statistiky obsahu:</h4>
        <ul>
            <li><strong>Profesní zkušenosti:</strong> <?= count($polozky_podle_typu['profese'] ?? []) ?> položek</li>
            <li><strong>Dovednosti:</strong> <?= count($polozky_podle_typu['dovednost'] ?? []) ?> kategorií</li>
            <li><strong>Vlastnosti:</strong> <?= count($polozky_podle_typu['vlastnost'] ?? []) ?> položek</li>
            <li><strong>Jazyky:</strong> <?= count($polozky_podle_typu['jazyk'] ?? []) ?> položek</li>
            <li><strong>Vzdělání:</strong> <?= count($polozky_podle_typu['vzdelani'] ?? []) ?> položek</li>
            <li><strong>Zájmy:</strong> <?= count($polozky_podle_typu['zajem'] ?? []) ?> položek</li>
        </ul>
    </div>

    <hr>

    <h4>Náhled URL:</h4>
    <p>Váš životopis je dostupný na adrese: <a href="/cv" target="_blank"><?= htmlspecialchars($_SERVER['HTTP_HOST']) ?>/cv</a></p>

    <hr>

    <h4>Tipy pro úpravu:</h4>
    <ul>
        <li>V záložce <strong>Základní údaje</strong> nastavte své jméno, kontakty a profilovou fotografii</li>
        <li>Jednotlivé položky můžete řadit pomocí čísel v poli "Pořadí"</li>
        <li>U profesních zkušeností můžete použít formátovaný text pomocí editoru</li>
        <li>Nezapomeňte po všech úpravách kliknout na tlačítko "Uložit všechny změny"</li>
    </ul>
</div>