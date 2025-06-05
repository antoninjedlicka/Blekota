<?php
// admin/content/uzivatele.php
// Načtení dat z DB a vykreslení záložek + includ první záložky

require_once __DIR__ . '/../../databaze.php';

$pdo = blkt_db_connect();
$stmt = $pdo->query("
    SELECT blkt_id, blkt_jmeno, blkt_prijmeni, blkt_mail, blkt_admin
    FROM blkt_uzivatele
    ORDER BY blkt_jmeno, blkt_prijmeni
");
$uzivatele = $stmt->fetchAll();
?>

<nav class="blkt-tabs">
  <button class="active" data-tab="prehled">Přehled</button>
</nav>

<div id="tab-prehled" class="tab-content">
  <?php include __DIR__ . '/uzivatele/prehled.php'; ?>
</div>
