<?php
// admin/content/obrazky/prehled.php
// Předpokládá se, že $groups a $mesice už máte z parent skriptu
?>
<div class="section-toolbar" style="display:flex;gap:1rem;margin-bottom:1rem;">
  <div class="blkt-formular-skupina toolbar-search" style="max-width:250px;">
    <input type="text" id="blkt-search" placeholder=" " />
    <label for="blkt-search">Hledat</label>
  </div>
  <button id="blkt-add-image-btn" class="btn btn-new-user">Přidat obrázek</button>
</div>

<div class="admin-section">
  <?php if (!empty($groups)): ?>
    <?php foreach ($groups as $ym => $images):
      list($year, $mon) = explode('-', $ym);
      $label = $mesice[(int)$mon] . ' ' . $year;
    ?>
      <div class="month-divider"><span><?= $label ?></span></div>
      <div class="blkt-image-gallery" style="display:flex;flex-wrap:wrap;gap:1rem;">
        <?php foreach ($images as $obrazek): ?>
          <div class="blkt-image-card"
               data-id="<?= (int)$obrazek['blkt_id'] ?>"
               data-url="../media/upload/<?= htmlspecialchars($obrazek['blkt_filename'],ENT_QUOTES) ?>"
               data-title="<?= htmlspecialchars($obrazek['blkt_title'],ENT_QUOTES) ?>"
               data-alt="<?= htmlspecialchars($obrazek['blkt_alt'],ENT_QUOTES) ?>"
               data-desc="<?= htmlspecialchars($obrazek['blkt_description'],ENT_QUOTES) ?>"
               data-orig="<?= htmlspecialchars($obrazek['blkt_original_name'],ENT_QUOTES) ?>">
            <div class="blkt-thumb" style="position:relative;">
              <img src="../media/upload/<?= htmlspecialchars($obrazek['blkt_filename'],ENT_QUOTES) ?>"
                   alt="<?= htmlspecialchars($obrazek['blkt_alt'],ENT_QUOTES) ?>"
                   style="max-height:150px;object-fit:cover;">
              <div class="blkt-card-actions">
                <button type="button" data-action="edit"   class="btn btn-edit-image"><img src="../../../media/icons/edit.svg" alt="Upravit" title="Upravit" width="20" height="20"></button>
                <button type="button" data-action="delete" class="btn btn-delete-image"><img src="../../../media/icons/delete.svg" alt="Smazat" title="Smazat" width="20" height="20"></button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="info-message">V galerii zatím nejsou žádné obrázky.</p>
  <?php endif; ?>
</div>
