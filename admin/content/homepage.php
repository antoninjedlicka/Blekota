<?php
// admin/homepage.php
require_once __DIR__ . '/../databaze.php';
?>

<nav class="blkt-tabs">
    <button class="active" data-tab="prehled">Přehled</button>
</nav>

<div id="tab-prehled" class="tab-content active">
    <?php include __DIR__ . '/homepage/prehled.php'; ?>
</div>

<div class="blkt-sticky-save">
    <button type="submit" form="blkt-form-homepage" class="btn btn-save">Uložit všechny změny</button>
</div>

<script defer src="js/homepage.js"></script>
