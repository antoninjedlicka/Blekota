<?php
// admin/content/uzivatele.php
// Načtení dat z DB a vykreslení záložek + includ první záložky

require_once __DIR__ . '/../../databaze.php';

$pdo = blkt_db_connect();

// Načteme uživatele
$stmt = $pdo->query("
    SELECT 
        u.blkt_id, 
        u.blkt_jmeno, 
        u.blkt_prijmeni, 
        u.blkt_mail, 
        u.blkt_admin,
        u.blkt_idskupiny,
        s.blkt_nazev as skupina_nazev
    FROM blkt_uzivatele u
    LEFT JOIN blkt_skupiny s ON u.blkt_idskupiny = s.blkt_idskupiny
    ORDER BY u.blkt_jmeno, u.blkt_prijmeni
");
$uzivatele = $stmt->fetchAll();

// Načteme skupiny a role pro druhou záložku
$skupiny = blkt_get_skupiny();
$role = blkt_get_role();
?>

<nav class="blkt-tabs">
    <button class="active" data-tab="prehled">Přehled</button>
    <button data-tab="skupiny">Skupiny a role</button>
</nav>

<div id="tab-prehled" class="tab-content">
    <?php include __DIR__ . '/uzivatele/prehled.php'; ?>
</div>

<div id="tab-skupiny" class="tab-content" style="display:none">
    <?php include __DIR__ . '/uzivatele/skupiny.php'; ?>
</div>