<?php
// admin/content/prispevky/editor.php - OPRAVENÁ VERZE

require_once __DIR__ . '/../../databaze.php';
$pdo = blkt_db_connect();

// NEBEZPEČNÉ - náchylné na SQL injection!
// $cats = $pdo->query("SELECT DISTINCT blkt_kategorie FROM blkt_prispevky")->fetchAll(PDO::FETCH_COLUMN);

// BEZPEČNÉ - použití prepared statements
$stmt = $pdo->prepare("SELECT DISTINCT blkt_kategorie FROM blkt_prispevky WHERE blkt_kategorie IS NOT NULL AND blkt_kategorie != '' ORDER BY blkt_kategorie");
$stmt->execute();
$cats = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Načteme seznam všech štítků pro datalist
$tags = blkt_get_all_tags();
?>

<div class="editor-container" style="display:flex;flex-direction:column;height:100%;">
  <!-- Meta informace -->
  <div class="post-meta" style="display:flex;gap:1rem;margin-bottom:1rem;">
    <!-- Název příspěvku -->
    <div class="blkt-formular-skupina" style="flex:1;">
      <input type="text" id="blkt-post-title" name="blkt_nazev" placeholder=" " required>
      <label for="blkt-post-title">Název příspěvku</label>
    </div>
    <!-- Kategorie s escapováním -->
    <div class="blkt-formular-skupina" style="flex:1;">
      <input list="blkt-category-list" id="blkt-post-category" name="blkt_kategorie" placeholder=" " required>
      <label for="blkt-post-category">Kategorie</label>
      <datalist id="blkt-category-list">
        <?php foreach ($cats as $c): ?>
          <option value="<?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>">
        <?php endforeach; ?>
      </datalist>
    </div>
    <!-- Slug -->
    <div class="blkt-formular-skupina" style="flex:1;">
      <input type="text" id="blkt-post-slug" name="blkt_slug" placeholder=" " required>
      <label for="blkt-post-slug">Slug</label>
    </div>
    <!-- Štítky s escapováním -->
    <div class="blkt-formular-skupina" style="flex:1;">
      <input list="blkt-tags-list" id="blkt-post-tags" name="blkt_tags" placeholder=" ">
      <label for="blkt-post-tags">Štítky</label>
      <datalist id="blkt-tags-list">
        <?php foreach ($tags as $t): ?>
          <option value="<?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>">
        <?php endforeach; ?>
      </datalist>
    </div>
  </div>

  <!-- TinyMCE editor -->
  <textarea id="blkt-editor" name="blkt_obsah" style="flex:1; width:100%;"></textarea>

  <!-- Akční tlačítka -->
  <div class="editor-actions" style="margin-top:1rem; display:flex; gap:1rem;">
    <button type="button" id="blkt-post-cancel" class="btn btn-cancel">Zrušit</button>
    <button type="button" id="blkt-post-save"   class="btn btn-save">Uložit</button>
  </div>
</div>

<!-- Popup galerie -->
<div id="blkt-gallery-overlay" class="blkt-modal-overlay" style="display:none;"></div>
<div id="blkt-gallery-modal" class="blkt-modal" style="display:none;max-width:600px;">
  <div class="blkt-modal-header">
    <h3>Galerie obrázků</h3>
    <button class="blkt-modal-close">&times;</button>
  </div>
  <div class="blkt-modal-body">
    <div class="blkt-gallery-images"></div>
    <div class="blkt-formular-skupina">
      <select id="blkt-gallery-align" required>
        <option value="" disabled selected>Vyberte zarovnání...</option>
        <option value="">žádné</option>
        <option value="left">vlevo</option>
        <option value="right">vpravo</option>
        <option value="center">na střed</option>
      </select>
    </div>
    <div class="blkt-formular-skupina">
      <select id="blkt-gallery-display" required>
        <option value="" disabled selected>Zvolte chování obrázku...</option>
        <option value="inline">Průběžný s textem</option>
        <option value="block">Jako blok</option>
      </select>
    </div>
  </div>
  <div class="modal-actions" style="padding:1rem; display:flex; justify-content:flex-end; gap:1rem;">
    <button type="button" id="blkt-gallery-cancel" class="btn btn-cancel">Zrušit</button>
    <button type="button" id="blkt-gallery-insert" class="btn btn-save" disabled>Vložit</button>
  </div>
</div>