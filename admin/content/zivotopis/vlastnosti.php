<?php
// admin/content/zivotopis/vlastnosti.php
$vlastnosti = $polozky_podle_typu['vlastnost'] ?? [];
?>
<div class="blkt-admin-box">
    <h2>Vlastnosti</h2>
    <p>Přidejte své klíčové vlastnosti s krátkým popisem a ikonou (emoji).</p>

    <div id="blkt-vlastnosti-container">
        <?php foreach ($vlastnosti as $index => $vlastnost): ?>
            <div class="blkt-cv-vlastnost-editor" data-index="<?= $index ?>">
                <input type="hidden" name="vlastnosti[<?= $index ?>][id]" value="<?= $vlastnost['blkt_id'] ?>">
                <input type="hidden" name="vlastnosti[<?= $index ?>][typ]" value="vlastnost">

                <button type="button" class="blkt-odebrat-radek">✕</button>

                <div style="display:flex; gap:1rem;">
                    <div class="blkt-formular-skupina" style="flex:0 0 100px;">
                        <input type="text" name="vlastnosti[<?= $index ?>][ikona]" value="<?= htmlspecialchars($vlastnost['blkt_ikona']) ?>" placeholder=" " maxlength="2">
                        <label>Ikona</label>
                    </div>

                    <div class="blkt-formular-skupina" style="flex:1;">
                        <input type="text" name="vlastnosti[<?= $index ?>][nazev]" value="<?= htmlspecialchars($vlastnost['blkt_nazev']) ?>" placeholder=" " required>
                        <label>Název vlastnosti</label>
                    </div>

                    <div class="blkt-formular-skupina" style="flex:0 0 80px;">
                        <input type="number" name="vlastnosti[<?= $index ?>][poradi]" value="<?= $vlastnost['blkt_poradi'] ?>" placeholder=" ">
                        <label>Pořadí</label>
                    </div>
                </div>

                <div class="blkt-formular-skupina">
                    <input type="text" name="vlastnosti[<?= $index ?>][popis]" value="<?= htmlspecialchars($vlastnost['blkt_popis']) ?>" placeholder=" ">
                    <label>Popis vlastnosti</label>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" id="blkt-pridat-vlastnost" class="btn btn-new-user">Přidat vlastnost</button>

    <p style="margin-top:1rem;"><small>💡 Tip: Pro ikony můžete použít emoji. Na Windows: Win + . (tečka), na Mac: Cmd + Control + Space</small></p>
</div>

<!-- Šablona pro novou vlastnost -->
<script type="text/template" id="blkt-vlastnost-template">
    <div class="blkt-cv-vlastnost-editor" data-index="{{index}}">
        <input type="hidden" name="vlastnosti[{{index}}][id]" value="">
        <input type="hidden" name="vlastnosti[{{index}}][typ]" value="vlastnost">

        <button type="button" class="blkt-odebrat-radek">✕</button>

        <div style="display:flex; gap:1rem;">
            <div class="blkt-formular-skupina" style="flex:0 0 100px;">
                <input type="text" name="vlastnosti[{{index}}][ikona]" placeholder=" " maxlength="2">
                <label>Ikona</label>
            </div>

            <div class="blkt-formular-skupina" style="flex:1;">
                <input type="text" name="vlastnosti[{{index}}][nazev]" placeholder=" " required>
                <label>Název vlastnosti</label>
            </div>

            <div class="blkt-formular-skupina" style="flex:0 0 80px;">
                <input type="number" name="vlastnosti[{{index}}][poradi]" value="0" placeholder=" ">
                <label>Pořadí</label>
            </div>
        </div>

        <div class="blkt-formular-skupina">
            <input type="text" name="vlastnosti[{{index}}][popis]" placeholder=" ">
            <label>Popis vlastnosti</label>
        </div>
    </div>
</script>

<style>
    .blkt-cv-vlastnost-editor {
        position: relative;
        background: rgba(255,255,255,0.5);
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
</style>