<?php
// admin/content/gramatika/prehled.php
// Přehled všech nastavení české gramatiky a typografie
?>

<form id="blkt-form-gramatika" action="action/save_gramatika.php" method="post" class="nastaveni-form">

    <!-- Předložky a spojky -->
    <div class="blkt-admin-box">
        <h2>Předložky a spojky</h2>
        <p>Nastavení pro jednopísmenné předložky a spojky, za kterými se automaticky vloží nezalomitelná mezera (&amp;nbsp;),
            aby nezůstávaly na konci řádku.</p>

        <div class="blkt-formular-skupina">
            <div class="blkt-tag-input-wrapper" data-name="gramatika_predlozky">
                <?php
                $predlozky = array_filter(array_map('trim', explode(',', $gramatika_data['gramatika_predlozky'])));
                foreach ($predlozky as $predlozka):
                    ?>
                    <span class="blkt-tag">
                        <?= htmlspecialchars($predlozka) ?>
                        <button type="button" class="blkt-tag-remove" aria-label="Odebrat">×</button>
                    </span>
                <?php endforeach; ?>
                <input type="text" class="blkt-tag-input" placeholder="Přidat předložku...">
            </div>
            <input type="hidden" name="gramatika_predlozky" value="<?= htmlspecialchars($gramatika_data['gramatika_predlozky']) ?>" class="blkt-tag-hidden">
            <label><?= $gramatika_nastaveni['gramatika_predlozky']['nazev'] ?></label>
            <small class="form-help"><?= $gramatika_nastaveni['gramatika_predlozky']['popis'] ?></small>
        </div>

        <div class="blkt-formular-skupina">
            <div class="blkt-tag-input-wrapper" data-name="gramatika_spojky">
                <?php
                $spojky = array_filter(array_map('trim', explode(',', $gramatika_data['gramatika_spojky'])));
                foreach ($spojky as $spojka):
                    ?>
                    <span class="blkt-tag">
                        <?= htmlspecialchars($spojka) ?>
                        <button type="button" class="blkt-tag-remove" aria-label="Odebrat">×</button>
                    </span>
                <?php endforeach; ?>
                <input type="text" class="blkt-tag-input" placeholder="Přidat spojku...">
            </div>
            <input type="hidden" name="gramatika_spojky" value="<?= htmlspecialchars($gramatika_data['gramatika_spojky']) ?>" class="blkt-tag-hidden">
            <label><?= $gramatika_nastaveni['gramatika_spojky']['nazev'] ?></label>
            <small class="form-help"><?= $gramatika_nastaveni['gramatika_spojky']['popis'] ?></small>
        </div>
    </div>

    <!-- Zkratky -->
    <div class="blkt-admin-box">
        <h2>Zkratky</h2>
        <p>Seznam zkratek, za kterými se vloží nezalomitelná mezera. Zkratky oddělujte čárkou.</p>

        <div class="blkt-formular-skupina">
            <div class="blkt-tag-input-wrapper" data-name="gramatika_zkratky">
                <?php
                $zkratky = array_filter(array_map('trim', explode(',', $gramatika_data['gramatika_zkratky'])));
                foreach ($zkratky as $zkratka):
                    ?>
                    <span class="blkt-tag">
                        <?= htmlspecialchars($zkratka) ?>
                        <button type="button" class="blkt-tag-remove" aria-label="Odebrat">×</button>
                    </span>
                <?php endforeach; ?>
                <input type="text" class="blkt-tag-input" placeholder="Přidat zkratku...">
            </div>
            <input type="hidden" name="gramatika_zkratky" value="<?= htmlspecialchars($gramatika_data['gramatika_zkratky']) ?>" class="blkt-tag-hidden">
            <label><?= $gramatika_nastaveni['gramatika_zkratky']['nazev'] ?></label>
            <small class="form-help"><?= $gramatika_nastaveni['gramatika_zkratky']['popis'] ?></small>
        </div>
    </div>

    <!-- Jednotky a čísla -->
    <div class="blkt-admin-box">
        <h2>Čísla a jednotky</h2>
        <p>Nastavení pro správné formátování čísel a jednotek.</p>

        <div class="blkt-checkbox-skupina">
            <label class="role-checkbox-label">
                <input type="checkbox"
                       name="gramatika_cislovky"
                       value="1"
                       class="role-checkbox"
                    <?= $gramatika_data['gramatika_cislovky'] == '1' ? 'checked' : '' ?>>
                <div class="role-info">
                    <span class="role-name"><?= $gramatika_nastaveni['gramatika_cislovky']['nazev'] ?></span>
                    <span class="role-description"><?= $gramatika_nastaveni['gramatika_cislovky']['popis'] ?></span>
                </div>
            </label>
        </div>

        <div class="blkt-formular-skupina">
            <div class="blkt-tag-input-wrapper" data-name="gramatika_jednotky">
                <?php
                $jednotky = array_filter(array_map('trim', explode(',', $gramatika_data['gramatika_jednotky'])));
                foreach ($jednotky as $jednotka):
                    ?>
                    <span class="blkt-tag">
                        <?= htmlspecialchars($jednotka) ?>
                        <button type="button" class="blkt-tag-remove" aria-label="Odebrat">×</button>
                    </span>
                <?php endforeach; ?>
                <input type="text" class="blkt-tag-input" placeholder="Přidat jednotku...">
            </div>
            <input type="hidden" name="gramatika_jednotky" value="<?= htmlspecialchars($gramatika_data['gramatika_jednotky']) ?>" class="blkt-tag-hidden">
            <label><?= $gramatika_nastaveni['gramatika_jednotky']['nazev'] ?></label>
            <small class="form-help"><?= $gramatika_nastaveni['gramatika_jednotky']['popis'] ?></small>
        </div>
    </div>

    <!-- Typografické úpravy -->
    <div class="blkt-admin-box">
        <h2>Typografické úpravy</h2>
        <p>Automatické nahrazování znaků pro lepší typografii.</p>

        <div class="blkt-checkbox-skupina">
            <label class="role-checkbox-label">
                <input type="checkbox"
                       name="gramatika_uvozovky"
                       value="1"
                       class="role-checkbox"
                    <?= $gramatika_data['gramatika_uvozovky'] == '1' ? 'checked' : '' ?>>
                <div class="role-info">
                    <span class="role-name"><?= $gramatika_nastaveni['gramatika_uvozovky']['nazev'] ?></span>
                    <span class="role-description"><?= $gramatika_nastaveni['gramatika_uvozovky']['popis'] ?></span>
                </div>
            </label>
        </div>

        <div class="blkt-checkbox-skupina">
            <label class="role-checkbox-label">
                <input type="checkbox"
                       name="gramatika_pomlcky"
                       value="1"
                       class="role-checkbox"
                    <?= $gramatika_data['gramatika_pomlcky'] == '1' ? 'checked' : '' ?>>
                <div class="role-info">
                    <span class="role-name"><?= $gramatika_nastaveni['gramatika_pomlcky']['nazev'] ?></span>
                    <span class="role-description"><?= $gramatika_nastaveni['gramatika_pomlcky']['popis'] ?></span>
                </div>
            </label>
        </div>

        <div class="blkt-checkbox-skupina">
            <label class="role-checkbox-label">
                <input type="checkbox"
                       name="gramatika_tecky"
                       value="1"
                       class="role-checkbox"
                    <?= $gramatika_data['gramatika_tecky'] == '1' ? 'checked' : '' ?>>
                <div class="role-info">
                    <span class="role-name"><?= $gramatika_nastaveni['gramatika_tecky']['nazev'] ?></span>
                    <span class="role-description"><?= $gramatika_nastaveni['gramatika_tecky']['popis'] ?></span>
                </div>
            </label>
        </div>
    </div>

    <!-- Náhled -->
    <div class="blkt-admin-box">
        <h2>Náhled úprav</h2>
        <p>Zde můžete vidět, jak budou vypadat vaše texty po aplikování gramatických pravidel.</p>

        <div class="blkt-formular-skupina">
            <textarea id="blkt-gramatika-test-input"
                      rows="3"
                      placeholder=" ">Například s firmou ABC a s panem Novákem jsme šli k autu o 5 hodině. Cena byla cca 1000 Kč za 10 kg materiálu. "Citace v uvozovkách" a rozsah 10-20...</textarea>
            <label>Testovací text</label>
        </div>

        <div class="blkt-gramatika-nahled">
            <h4>Výsledek po úpravách:</h4>
            <div id="blkt-gramatika-test-output"></div>
        </div>
    </div>

</form>

<style>
    /* Tag input styly */
    .blkt-tag-input-wrapper {
        position: relative;
        width: 100%;
        min-height: 48px;
        background: rgba(255, 255, 255, 0.8);
        border: 2px solid var(--blkt-border-light);
        border-radius: 8px;
        padding: 0.5rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
        transition: all 0.3s ease;
        cursor: text;
    }

    .blkt-tag-input-wrapper:focus-within {
        background: white;
        border-color: var(--blkt-primary);
        box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.1);
    }

    .blkt-tag-input-wrapper.has-focus label {
        top: -0.75rem;
        left: 0.75rem;
        transform: translateY(0);
        font-size: 0.85rem;
        color: var(--blkt-primary);
        background: white;
        border-radius: 4px;
        padding: 0 0.5rem;
    }

    .blkt-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #f5f7fa, #e9ecef);
        border: 1px solid var(--blkt-border);
        border-radius: 20px;
        padding: 0.25rem 0.75rem;
        font-size: 0.9em;
        color: var(--blkt-text);
        transition: all 0.2s ease;
    }

    .blkt-tag:hover {
        background: linear-gradient(135deg, #e9ecef, #dee2e6);
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .blkt-tag-remove {
        background: none;
        border: none;
        color: var(--blkt-text-light);
        cursor: pointer;
        padding: 0;
        margin: 0;
        font-size: 1.2em;
        line-height: 1;
        transition: all 0.2s ease;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .blkt-tag-remove:hover {
        color: var(--blkt-danger);
        background: rgba(231, 76, 60, 0.1);
        transform: rotate(90deg);
    }

    .blkt-tag-input {
        border: none;
        outline: none;
        background: transparent;
        flex: 1;
        min-width: 100px;
        font-size: 1em;
        font-family: inherit;
        padding: 0.25rem 0;
    }

    .blkt-tag-input::placeholder {
        color: var(--blkt-text-muted);
        font-style: italic;
    }

    /* Hidden input pro odeslání dat */
    .blkt-tag-hidden {
        position: absolute;
        left: -9999px;
    }

    /* Specifické styly pro checkboxy - použijeme stejné jako u skupin */
    .blkt-checkbox-skupina {
        margin: 1rem 0;
    }

    /* Nápověda u formulářových polí */
    .form-help {
        display: block;
        margin-top: 0.25rem;
        color: var(--blkt-text-light);
        font-size: 0.85em;
        font-style: italic;
    }

    /* Náhled */
    .blkt-gramatika-nahled {
        margin-top: 1.5rem;
        padding: 1.5rem;
        background: white;
        border-radius: 8px;
        border: 2px solid var(--blkt-border-light);
    }

    .blkt-gramatika-nahled h4 {
        margin: 0 0 1rem 0;
        color: var(--blkt-primary);
    }

    #blkt-gramatika-test-output {
        font-size: 1.1em;
        line-height: 1.8;
        color: var(--blkt-text);
    }

    /* Zvýraznění nezalomitelných mezer v náhledu */
    #blkt-gramatika-test-output .nbsp {
        background: rgba(52, 152, 219, 0.2);
        padding: 0 2px;
        border-radius: 3px;
    }
</style>