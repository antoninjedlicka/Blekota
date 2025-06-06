<?php
// admin/content/zivotopis/vzdelani.php
$vzdelani = $polozky_podle_typu['vzdelani'] ?? [];
$zajmy = $polozky_podle_typu['zajem'] ?? [];
?>
<div class="blkt-admin-box">
    <h2>Vzdělání</h2>
    <p>Uveďte své dosažené vzdělání.</p>

    <div id="blkt-vzdelani-container">
        <?php foreach ($vzdelani as $index => $skola): ?>
            <div class="blkt-cv-vzdelani-editor" data-index="<?= $index ?>">
                <input type="hidden" name="vzdelani[<?= $index ?>][id]" value="<?= $skola['blkt_id'] ?>">
                <input type="hidden" name="vzdelani[<?= $index ?>][typ]" value="vzdelani">

                <button type="button" class="blkt-odebrat-radek">✕</button>

                <div class="blkt-formular-skupina">
                    <input type="number" name="vzdelani[<?= $index ?>][poradi]" value="<?= $skola['blkt_poradi'] ?>" placeholder=" ">
                    <label>Pořadí</label>
                </div>

                <div class="blkt-formular-skupina">
                    <input type="text" name="vzdelani[<?= $index ?>][nazev]" value="<?= htmlspecialchars($skola['blkt_nazev']) ?>" placeholder=" " required>
                    <label>Název školy</label>
                </div>

                <div style="display:flex; gap:1rem;">
                    <div class="blkt-formular-skupina" style="flex:1;">
                        <input type="text" name="vzdelani[<?= $index ?>][datum_od]" value="<?= htmlspecialchars($skola['blkt_datum_od']) ?>" placeholder=" ">
                        <label>Rok od</label>
                    </div>

                    <div class="blkt-formular-skupina" style="flex:1;">
                        <input type="text" name="vzdelani[<?= $index ?>][datum_do]" value="<?= htmlspecialchars($skola['blkt_datum_do']) ?>" placeholder=" ">
                        <label>Rok do</label>
                    </div>
                </div>

                <div class="blkt-formular-skupina">
                    <textarea name="vzdelani[<?= $index ?>][popis]" rows="2" placeholder=" "><?= htmlspecialchars($skola['blkt_popis']) ?></textarea>
                    <label>Popis (obor, zaměření, úspěchy)</label>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" id="blkt-pridat-vzdelani" class="btn btn-new-user">Přidat vzdělání</button>
</div>

<div class="blkt-admin-box">
    <h2>Zájmy a koníčky</h2>
    <p>Uveďte své zájmy oddělené čárkou.</p>

    <?php
    $zajmy_text = '';
    if (!empty($zajmy)) {
        $zajmy_text = $zajmy[0]['blkt_tagy'] ?? '';
    }
    ?>

    <input type="hidden" name="zajmy[0][id]" value="<?= $zajmy[0]['blkt_id'] ?? '' ?>">
    <input type="hidden" name="zajmy[0][typ]" value="zajem">

    <div class="blkt-formular-skupina">
        <textarea name="zajmy[0][tagy]" rows="3" placeholder=" "><?= htmlspecialchars($zajmy_text) ?></textarea>
        <label>Zájmy (oddělené čárkou)</label>
    </div>
</div>

<!-- Šablona pro nové vzdělání -->
<script type="text/template" id="blkt-vzdelani-template">
    <div class="blkt-cv-vzdelani-editor" data-index="{{index}}">
        <input type="hidden" name="vzdelani[{{index}}][id]" value="">
        <input type="hidden" name="vzdelani[{{index}}][typ]" value="vzdelani">

        <button type="button" class="blkt-odebrat-radek">✕</button>

        <div class="blkt-formular-skupina">
            <input type="number" name="vzdelani[{{index}}][poradi]" value="0" placeholder=" ">
            <label>Pořadí</label>
        </div>

        <div class="blkt-formular-skupina">
            <input type="text" name="vzdelani[{{index}}][nazev]" placeholder=" " required>
            <label>Název školy</label>
        </div>

        <div style="display:flex; gap:1rem;">
            <div class="blkt-formular-skupina" style="flex:1;">
                <input type="text" name="vzdelani[{{index}}][datum_od]" placeholder=" ">
                <label>Rok od</label>
            </div>

            <div class="blkt-formular-skupina" style="flex:1;">
                <input type="text" name="vzdelani[{{index}}][datum_do]" placeholder=" ">
                <label>Rok do</label>
            </div>
        </div>

        <div class="blkt-formular-skupina">
            <textarea name="vzdelani[{{index}}][popis]" rows="2" placeholder=" "></textarea>
            <label>Popis (obor, zaměření, úspěchy)</label>
        </div>
    </div>
</script>

<style>
    .blkt-cv-vzdelani-editor {
        position: relative;
        background: rgba(255,255,255,0.5);
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
</style>