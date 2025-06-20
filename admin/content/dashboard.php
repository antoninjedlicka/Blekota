<?php
// admin/content/dashboard.php
// Načtení databází
require_once __DIR__ . '/../databaze.php';
?>
<nav class="blkt-tabs">
  <button class="active" data-tab="prehled">Přehled</button>
</nav>

<div id="tab-prehled" class="tab-content">
  <?php include __DIR__ . '/dashboard/prehled.php'; ?>
</div>
