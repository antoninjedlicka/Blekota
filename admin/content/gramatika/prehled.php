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
            <input type="text"
                   name="gramatika_predlozky"
                   value="<?= htmlspecialchars($gramatika_data['gramatika_predlozky']) ?>"
                   placeholder=" "
                   pattern="[a-zA-Z,\s]*"
                   title="Pouze písmena oddělená čárkami">
            <label><?= $gramatika_nastaveni['gramatika_predlozky']['nazev'] ?></label>
            <small class="form-help"><?= $gramatika_nastaveni['gramatika_predlozky']['popis'] ?></small>
        </div>

        <div class="blkt-formular-skupina">
            <input type="text"
                   name="gramatika_spojky"
                   value="<?= htmlspecialchars($gramatika_data['gramatika_spojky']) ?>"
                   placeholder=" "
                   pattern="[a-zA-Z,\s]*"
                   title="Pouze písmena oddělená čárkami">
            <label><?= $gramatika_nastaveni['gramatika_spojky']['nazev'] ?></label>
            <small class="form-help"><?= $gramatika_nastaveni['gramatika_spojky']['popis'] ?></small>
        </div>
    </div>

    <!-- Zkratky -->
    <div class="blkt-admin-box">
        <h2>Zkratky</h2>
        <p>Seznam zkratek, za kterými se vloží nezalomitelná mezera. Zkratky oddělujte čárkou.</p>

        <div class="blkt-formular-skupina">
            <textarea name="gramatika_zkratky"
                      rows="3"
                      placeholder=" "><?= htmlspecialchars($gramatika_data['gramatika_zkratky']) ?></textarea>
            <label><?= $gramatika_nastaveni['gramatika_zkratky']['nazev'] ?></label>
            <small class="form-help"><?= $gramatika_nastaveni['gramatika_zkratky']['popis'] ?></small>
        </div>
    </div>

    <!-- Jednotky a čísla -->
    <div class="blkt-admin-box">
        <h2>Čísla a jednotky</h2>
        <p>Nastavení pro správné formátování čísel a jednotek.</p>

        <div class="blkt-checkbox-skupina">
            <label class="blkt-checkbox-label">
                <input type="checkbox"
                       name="gramatika_cislovky"
                       value="1"
                    <?= $gramatika_data['gramatika_cislovky'] == '1' ? 'checked' : '' ?>>
                <span class="checkbox-custom"></span>
                <div>
                    <strong><?= $gramatika_nastaveni['gramatika_cislovky']['nazev'] ?></strong>
                    <small><?= $gramatika_nastaveni['gramatika_cislovky']['popis'] ?></small>
                </div>
            </label>
        </div>

        <div class="blkt-formular-skupina">
            <textarea name="gramatika_jednotky"
                      rows="3"
                      placeholder=" "><?= htmlspecialchars($gramatika_data['gramatika_jednotky']) ?></textarea>
            <label><?= $gramatika_nastaveni['gramatika_jednotky']['nazev'] ?></label>
            <small class="form-help"><?= $gramatika_nastaveni['gramatika_jednotky']['popis'] ?></small>
        </div>
    </div>

    <!-- Typografické úpravy -->
    <div class="blkt-admin-box">
        <h2>Typografické úpravy</h2>
        <p>Automatické nahrazování znaků pro lepší typografii.</p>

        <div class="blkt-checkbox-skupina">
            <label class="blkt-checkbox-label">
                <input type="checkbox"
                       name="gramatika_uvozovky"
                       value="1"
                    <?= $gramatika_data['gramatika_uvozovky'] == '1' ? 'checked' : '' ?>>
                <span class="checkbox-custom"></span>
                <div>
                    <strong><?= $gramatika_nastaveni['gramatika_uvozovky']['nazev'] ?></strong>
                    <small><?= $gramatika_nastaveni['gramatika_uvozovky']['popis'] ?></small>
                </div>
            </label>
        </div>

        <div class="blkt-checkbox-skupina">
            <label class="blkt-checkbox-label">
                <input type="checkbox"
                       name="gramatika_pomlcky"
                       value="1"
                    <?= $gramatika_data['gramatika_pomlcky'] == '1' ? 'checked' : '' ?>>
                <span class="checkbox-custom"></span>
                <div>
                    <strong><?= $gramatika_nastaveni['gramatika_pomlcky']['nazev'] ?></strong>
                    <small><?= $gramatika_nastaveni['gramatika_pomlcky']['popis'] ?></small>
                </div>
            </label>
        </div>

        <div class="blkt-checkbox-skupina">
            <label class="blkt-checkbox-label">
                <input type="checkbox"
                       name="gramatika_tecky"
                       value="1"
                    <?= $gramatika_data['gramatika_tecky'] == '1' ? 'checked' : '' ?>>
                <span class="checkbox-custom"></span>
                <div>
                    <strong><?= $gramatika_nastaveni['gramatika_tecky']['nazev'] ?></strong>
                    <small><?= $gramatika_nastaveni['gramatika_tecky']['popis'] ?></small>
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
    /* Specifické styly pro checkboxy */
    .blkt-checkbox-skupina {
        margin: 1.5rem 0;
    }

    .blkt-checkbox-label {
        display: flex;
        align-items: flex-start;
        cursor: pointer;
        padding: 1rem;
        background: var(--blkt-glass-light);
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
    }

    .blkt-checkbox-label:hover {
        background: rgba(52, 152, 219, 0.05);
        transform: translateX(5px);
    }

    .blkt-checkbox-label input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .checkbox-custom {
        width: 24px;
        height: 24px;
        border: 2px solid var(--blkt-border);
        border-radius: 6px;
        margin-right: 1rem;
        position: relative;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .blkt-checkbox-label input[type="checkbox"]:checked ~ .checkbox-custom {
        background: linear-gradient(135deg, var(--blkt-primary), var(--blkt-primary-light));
        border-color: var(--blkt-primary);
    }

    .blkt-checkbox-label input[type="checkbox"]:checked ~ .checkbox-custom::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-weight: bold;
        font-size: 16px;
    }

    .blkt-checkbox-label div {
        flex: 1;
    }

    .blkt-checkbox-label strong {
        display: block;
        margin-bottom: 0.25rem;
        color: var(--blkt-text);
    }

    .blkt-checkbox-label small {
        color: var(--blkt-text-light);
        font-size: 0.9em;
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