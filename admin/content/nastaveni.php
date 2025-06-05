<?php
// admin/content/nastaveni.php
// Statické vykreslení záložek a include první záložky


// Načteme připojení k databázi a funkce
require_once __DIR__ . '/../databaze.php';

?>
<nav class="blkt-tabs">
  <button class="active" data-tab="prehled">Přehled</button>
</nav>

<div id="tab-prehled" class="tab-content">
  <?php include __DIR__ . '/nastaveni/prehled.php'; ?>
</div>
<div class="blkt-sticky-save">
    <button type="submit" form="blkt-form-seo" class="btn btn-save">Uložit všechny změny</button>
</div>
