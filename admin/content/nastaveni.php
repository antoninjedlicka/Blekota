<?php
// admin/content/nastaveni.php
// Hlavní soubor sekce nastavení

require_once __DIR__ . '/../databaze.php';
?>

<nav class="blkt-tabs">
    <button class="active" data-tab="prehled">Přehled</button>
</nav>

<div id="tab-prehled" class="tab-content">
    <?php include __DIR__ . '/nastaveni/prehled.php'; ?>
</div>

<div class="blkt-sticky-save">
    <button type="submit" form="blkt-form-nastaveni" class="btn btn-save">Uložit změny</button>
</div>

<script defer src="js/nastaveni.js"></script>