<?php
// admin/content/zivotopis/jazyky.php
$jazyky = $polozky_podle_typu['jazyk'] ?? [];
?>
<div class="blkt-admin-box">
    <h2>Jazykové znalosti</h2>
    <p>Uveďte jazyky, které ovládáte, včetně úrovně znalostí.</p>

    <div id="blkt-jazyky-container">
        <?php foreach ($jazyky as $index => $jazyk): ?>
            <div class="blkt-cv-jazyk-editor" data-index="<?= $index ?>">
                <input type="hidden" name="jazyky[<?= $index ?>][id]" value="<?= $jazyk['blkt_id'] ?>">
                <input type="hidden" name="jazyky[<?= $index ?>][typ]" value="jazyk">

                <button type="button" class="blkt-odebrat-radek">✕</button>

                <div style="display:flex; gap:1rem;">
                    <div class="blkt-formular-skupina" style="flex:0 0 100px;">
                        <input type="text" name="jazyky[<?= $index ?>][ikona]" value="<?= htmlspecialchars($jazyk['blkt_ikona']) ?>" placeholder=" " maxlength="4">
                        <label>Vlajka</label>
                    </div>

                    <div class="blkt-formular-skupina" style="flex:1;">
                        <input type="text" name="jazyky[<?= $index ?>][nazev]" value="<?= htmlspecialchars($jazyk['blkt_nazev']) ?>" placeholder=" " required>
                        <label>Název jazyka</label>
                    </div>

                    <div class="blkt-formular-skupina" style="flex:1;">
                        <select name="jazyky[<?= $index ?>][uroven]" required>
                            <option value="" disabled <?= empty($jazyk['blkt_uroven']) ? 'selected' : '' ?>></option>
                            <option value="Rodilý mluvčí" <?= $jazyk['blkt_uroven'] === 'Rodilý mluvčí' ? 'selected' : '' ?>>Rodilý mluvčí</option>
                            <option value="Pokročilá" <?= $jazyk['blkt_uroven'] === 'Pokročilá' ? 'selected' : '' ?>>Pokročilá</option>
                            <option value="Mírně pokročilá" <?= $jazyk['blkt_uroven'] === 'Mírně pokročilá' ? 'selected' : '' ?>>Mírně pokročilá</option>
                            <option value="Středně pokročilá" <?= $jazyk['blkt_uroven'] === 'Středně pokročilá' ? 'selected' : '' ?>>Středně pokročilá</option>
                            <option value="Základní" <?= $jazyk['blkt_uroven'] === 'Základní' ? 'selected' : '' ?>>Základní</option>
                        </select>
                        <label>Úroveň</label>
                    </div>

                    <div class="blkt-formular-skupina" style="flex:0 0 80px;">
                        <input type="number" name="jazyky[<?= $index ?>][poradi]" value="<?= $jazyk['blkt_poradi'] ?>" placeholder=" ">
                        <label>Pořadí</label>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" id="blkt-pridat-jazyk" class="btn btn-new-user">Přidat jazyk</button>

    <p style="margin-top:1rem;"><small>💡 Tip: Pro vlajky použijte emoji vlajek (např. 🇨🇿, 🇬🇧, 🇪🇸)</small></p>
</div>

<!-- Šablona pro nový jazyk -->
<script type="text/template" id="blkt-jazyk-template">
    <div class="blkt-cv-jazyk-editor" data-index="{{index}}">
        <input type="hidden" name="jazyky[{{index}}][id]" value="">
        <input type="hidden" name="jazyky[{{index}}][typ]" value="jazyk">

        <button type="button" class="blkt-odebrat-radek">✕</button>

        <div style="display:flex; gap:1rem;">
            <div class="blkt-formular-skupina" style="flex:0 0 100px;">
                <input type="text" name="jazyky[{{index}}][ikona]" placeholder=" " maxlength="4">
                <label>Vlajka</label>
            </div>

            <div class="blkt-formular-skupina" style="flex:1;">
                <input type="text" name="jazyky[{{index}}][nazev]" placeholder=" " required>
                <label>Název jazyka</label>
            </div>

            <div class="blkt-formular-skupina" style="flex:1;">
                <select name="jazyky[{{index}}][uroven]" required>
                    <option value="" disabled selected></option>
                    <option value="Rodilý mluvčí">Rodilý mluvčí</option>
                    <option value="Pokročilá">Pokročilá</option>
                    <option value="Mírně pokročilá">Mírně pokročilá</option>
                    <option value="Středně pokročilá">Středně pokročilá</option>
                    <option value="Základní">Základní</option>
                </select>
                <label>Úroveň</label>
            </div>

            <div class="blkt-formular-skupina" style="flex:0 0 80px;">
                <input type="number" name="jazyky[{{index}}][poradi]" value="0" placeholder=" ">
                <label>Pořadí</label>
            </div>
        </div>
    </div>
</script>

<style>
    .blkt-cv-jazyk-editor {
        position: relative;
        background: rgba(255,255,255,0.5);
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
</style>