<?php
// admin/content/prispevky.php

// Načteme připojení k databázi a funkce
require_once __DIR__ . '/../databaze.php';


$pdo = blkt_db_connect();
// Načteme příspěvky společně s detaily (slug + štítky)
$prispevky = $pdo->query(
    "SELECT
        p.blkt_id,
        p.blkt_nazev,
        p.blkt_kategorie,
        p.blkt_obsah,
        d.blkt_slug,
        d.blkt_tags
     FROM blkt_prispevky AS p
     LEFT JOIN blkt_obsah_detaily AS d
       ON d.blkt_parent_id = p.blkt_id
       AND d.blkt_type = 'post'
     ORDER BY p.blkt_id DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<nav class="blkt-tabs">
  <button class="active" data-tab="prehled">Přehled</button>
  <button data-tab="editor">Editor</button>
</nav>

<div id="tab-prehled" class="tab-content">
  <?php include __DIR__ . '/prispevky/prehled.php'; ?>
</div>

<div id="tab-editor" class="tab-content" style="display:none">
  <?php include __DIR__ . '/prispevky/editor.php'; ?>
</div>
