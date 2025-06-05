<?php
// admin/content/prispevky/prehled.php
?>
<div id="blkt-posts-overview">
  <div class="section-toolbar">
    <button class="btn btn-new-post" id="add-post-btn">Přidat příspěvek</button>
  </div>
  <table id="posts-table">
    <thead>
      <tr><th>ID</th><th>Název</th><th>Kategorie</th></tr>
    </thead>
    <tbody>
      <?php foreach ($prispevky as $p): ?>
  <tr
    data-id="<?= $p['blkt_id'] ?>"
    data-nazev="<?= htmlspecialchars($p['blkt_nazev'], ENT_QUOTES) ?>"
    data-kategorie="<?= htmlspecialchars($p['blkt_kategorie'], ENT_QUOTES) ?>"
    data-obsah="<?= htmlspecialchars($p['blkt_obsah'],    ENT_QUOTES) ?>"
    data-slug="<?= htmlspecialchars($p['blkt_slug'],       ENT_QUOTES) ?>"
    data-tags="<?= htmlspecialchars($p['blkt_tags'],       ENT_QUOTES) ?>"
  >
    <td><?= $p['blkt_id'] ?></td>
    <td><?= htmlspecialchars($p['blkt_nazev']) ?></td>
    <td><?= htmlspecialchars($p['blkt_kategorie']) ?></td>
    <!-- další sloupce… -->
  </tr>
<?php endforeach; ?>

    </tbody>
  </table>
</div>