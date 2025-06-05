<?php
// admin/content/obrazky.php

require_once __DIR__ . '/../databaze.php';
$pdo = blkt_db_connect();

// Vytáhneme a seskupíme obrázky podle roku-měsíce
$stmt = $pdo->prepare("
    SELECT *
      FROM blkt_images
     ORDER BY blkt_created_at DESC
");
$stmt->execute();
$obrazky = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Česká jména měsíců
$mesice = [
  1=>'Leden', 2=>'Únor', 3=>'Březen', 4=>'Duben',
  5=>'Květen',6=>'Červen',7=>'Červenec',8=>'Srpen',
  9=>'Září',10=>'Říjen',11=>'Listopad',12=>'Prosinec'
];

// Seskupíme podle “YYYY-MM”
$groups = [];
foreach ($obrazky as $img) {
  $ts  = strtotime($img['blkt_created_at']);
  $key = date('Y-m', $ts);
  $groups[$key][] = $img;
}
?>

<nav class="blkt-tabs">
  <button class="active" data-tab="prehled">Přehled</button>
  <button data-tab="editor">Editor</button>
</nav>

<div id="tab-prehled" class="tab-content">
  <?php include __DIR__ . '/obrazky/prehled.php'; ?>
</div>

<div id="tab-editor" class="tab-content" style="display:none">
  <?php include __DIR__ . '/obrazky/editor.php'; ?>
</div>

