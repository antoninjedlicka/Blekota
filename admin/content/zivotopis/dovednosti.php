<?php
// admin/content/zivotopis/dovednosti.php
$dovednosti = $polozky_podle_typu['dovednost'] ?? [];
?>
<div class="blkt-admin-box">
    <h2>Dovednosti a technologie</h2>
    <p>Rozdělte své dovednosti do kategorií. Jednotlivé dovednosti oddělte čárkou.</p>

    <div id="blkt-dovednosti-container">
        <?php foreach ($dovednosti as $index => $kategorie): ?>
            <div class="blkt-cv-dovednost-editor" data-index="<?= $index ?>">
                <input type="hidden" name="dovednosti[<?= $index ?>][id]" value="<?= $kategorie['blkt_id'] ?>">
                <input type="hidden" name="dovednosti[<?= $index ?>][typ]" value="dovednost">

                <button type="button" class="blkt-odebrat-radek">✕</button>

                <div class="blkt-formular-skupina">
                    <input type="number" name="dovednosti[<?= $index ?>][poradi]" value="<?= $kategorie['blkt_poradi'] ?>" placeholder=" ">
                    <label>Pořadí</label>
                </div>

                <div class="blkt-formular-skupina">
                    <input type="text" name="dovednosti[<?= $index ?>][podnazev]" value="<?= htmlspecialchars($kategorie['blkt_podnazev']) ?>" placeholder=" " required>
                    <label>Název kategorie (např. Webové technologie)</label>
                </div>

                <div class="blkt-formular-skupina">
                    <input type="text" name="dovednosti[<?= $index ?>][tagy]" value="<?= htmlspecialchars($kategorie['blkt_tagy']) ?>" placeholder=" " required>
                    <label>Dovednosti (oddělené čárkou, např. HTML,CSS,JavaScript)</label>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" id="blkt-pridat-dovednost" class="btn btn-new-user">Přidat kategorii dovedností</button>
</div>

<!-- Šablona pro novou kategorii dovedností -->
<script type="text/template" id="blkt-dovednost-template">
    <div class="blkt-cv-dovednost-editor" data-index="{{index}}">
        <input type="hidden" name="dovednosti[{{index}}][id]" value="">
        <input type="hidden" name="dovednosti[{{index}}][typ]" value="dovednost">

        <button type="button" class="blkt-odebrat-radek">✕</button>

        <div class="blkt-formular-skupina">
            <input type="number" name="dovednosti[{{index}}][poradi]" value="0" placeholder=" ">
            <label>Pořadí</label>
        </div>

        <div class="blkt-formular-skupina">
            <input type="text" name="dovednosti[{{index}}][podnazev]" placeholder=" " required>
            <label>Název kategorie (např. Webové technologie)</label>
        </div>

        <div class="blkt-formular-skupina">
            <input type="text" name="dovednosti[{{index}}][tagy]" placeholder=" " required>
            <label>Dovednosti (oddělené čárkou, např. HTML,CSS,JavaScript)</label>
        </div>
    </div>
</script>

<style>
    .blkt-cv-dovednost-editor {
        position: relative;
        background: rgba(255,255,255,0.5);
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
</style>