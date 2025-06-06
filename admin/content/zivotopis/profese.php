<?php
// admin/content/zivotopis/profese.php
$profese = $polozky_podle_typu['profese'] ?? [];
?>
<div class="blkt-admin-box">
    <h2>Profesní zkušenosti</h2>
    <p>Seřaďte své pracovní pozice od nejnovější po nejstarší.</p>

    <div id="blkt-profese-container">
        <?php foreach ($profese as $index => $pozice): ?>
            <div class="blkt-cv-pozice-editor" data-index="<?= $index ?>">
                <input type="hidden" name="profese[<?= $index ?>][id]" value="<?= $pozice['blkt_id'] ?>">
                <input type="hidden" name="profese[<?= $index ?>][typ]" value="profese">

                <div class="blkt-cv-pozice-header">
                    <h4>Pozice #<?= $index + 1 ?></h4>
                    <button type="button" class="blkt-odebrat-pozici btn btn-delete-user">Odebrat</button>
                </div>

                <div class="blkt-formular-skupina">
                    <input type="number" name="profese[<?= $index ?>][poradi]" value="<?= $pozice['blkt_poradi'] ?>" placeholder=" ">
                    <label>Pořadí</label>
                </div>

                <div class="blkt-formular-skupina">
                    <input type="text" name="profese[<?= $index ?>][nazev]" value="<?= htmlspecialchars($pozice['blkt_nazev']) ?>" placeholder=" " required>
                    <label>Název pozice</label>
                </div>

                <div class="blkt-formular-skupina">
                    <input type="text" name="profese[<?= $index ?>][podnazev]" value="<?= htmlspecialchars($pozice['blkt_podnazev']) ?>" placeholder=" " required>
                    <label>Název společnosti</label>
                </div>

                <div style="display:flex; gap:1rem;">
                    <div class="blkt-formular-skupina" style="flex:1;">
                        <input type="text" name="profese[<?= $index ?>][datum_od]" value="<?= htmlspecialchars($pozice['blkt_datum_od']) ?>" placeholder=" ">
                        <label>Datum od (např. 10/2020)</label>
                    </div>

                    <div class="blkt-formular-skupina" style="flex:1;">
                        <input type="text" name="profese[<?= $index ?>][datum_do]" value="<?= htmlspecialchars($pozice['blkt_datum_do']) ?>" placeholder=" ">
                        <label>Datum do (např. 03/2025)</label>
                    </div>
                </div>

                <div class="blkt-formular-skupina">
                    <textarea name="profese[<?= $index ?>][popis]" rows="3" placeholder=" "><?= htmlspecialchars($pozice['blkt_popis']) ?></textarea>
                    <label>Krátký popis pozice</label>
                </div>

                <div class="blkt-formular-skupina">
                    <label style="position:static; margin-bottom:0.5rem; display:block;">Detailní popis činností:</label>
                    <textarea class="blkt-tinymce-editor" name="profese[<?= $index ?>][obsah]"><?= htmlspecialchars($pozice['blkt_obsah']) ?></textarea>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" id="blkt-pridat-profesi" class="btn btn-new-user">Přidat pozici</button>
</div>

<!-- Šablona pro novou pozici -->
<script type="text/template" id="blkt-profese-template">
    <div class="blkt-cv-pozice-editor" data-index="{{index}}">
        <input type="hidden" name="profese[{{index}}][id]" value="">
        <input type="hidden" name="profese[{{index}}][typ]" value="profese">

        <div class="blkt-cv-pozice-header">
            <h4>Nová pozice</h4>
            <button type="button" class="blkt-odebrat-pozici btn btn-delete-user">Odebrat</button>
        </div>

        <div class="blkt-formular-skupina">
            <input type="number" name="profese[{{index}}][poradi]" value="0" placeholder=" ">
            <label>Pořadí</label>
        </div>

        <div class="blkt-formular-skupina">
            <input type="text" name="profese[{{index}}][nazev]" placeholder=" " required>
            <label>Název pozice</label>
        </div>

        <div class="blkt-formular-skupina">
            <input type="text" name="profese[{{index}}][podnazev]" placeholder=" " required>
            <label>Název společnosti</label>
        </div>

        <div style="display:flex; gap:1rem;">
            <div class="blkt-formular-skupina" style="flex:1;">
                <input type="text" name="profese[{{index}}][datum_od]" placeholder=" ">
                <label>Datum od (např. 10/2020)</label>
            </div>

            <div class="blkt-formular-skupina" style="flex:1;">
                <input type="text" name="profese[{{index}}][datum_do]" placeholder=" ">
                <label>Datum do (např. 03/2025)</label>
            </div>
        </div>

        <div class="blkt-formular-skupina">
            <textarea name="profese[{{index}}][popis]" rows="3" placeholder=" "></textarea>
            <label>Krátký popis pozice</label>
        </div>

        <div class="blkt-formular-skupina">
            <label style="position:static; margin-bottom:0.5rem; display:block;">Detailní popis činností:</label>
            <textarea class="blkt-tinymce-editor" name="profese[{{index}}][obsah]"></textarea>
        </div>
    </div>
</script>

<style>
    .blkt-cv-pozice-editor {
        background: rgba(255,255,255,0.5);
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .blkt-cv-pozice-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .blkt-cv-pozice-header h4 {
        margin: 0;
    }
</style>