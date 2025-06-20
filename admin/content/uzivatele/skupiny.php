<?php
// admin/content/uzivatele/skupiny.php
// Záložka pro správu skupin a rolí
?>
<div class="section-toolbar">
    <button class="btn btn-new-user" id="add-group-btn">Přidat skupinu</button>
</div>

<table id="groups-table">
    <thead>
    <tr>
        <th>Název skupiny</th>
        <th>Popis</th>
        <th>Počet uživatelů</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($skupiny as $skupina):
        $pocet_uzivatelu = blkt_pocet_uzivatelu_ve_skupine($skupina['blkt_idskupiny']);

        // Získáme názvy rolí pro tuto skupinu
        $role_ids = !empty($skupina['blkt_role'])
            ? array_map('trim', explode(',', $skupina['blkt_role']))
            : [];
        $skupina_role = blkt_get_role_by_ids($role_ids);
        $role_nazvy = array_column($skupina_role, 'blkt_nazev');
        ?>
        <tr
            data-id="<?= $skupina['blkt_idskupiny'] ?>"
            data-nazev="<?= htmlspecialchars($skupina['blkt_nazev']) ?>"
            data-popis="<?= htmlspecialchars($skupina['blkt_popis']) ?>"
            data-role="<?= htmlspecialchars($skupina['blkt_role']) ?>"
            data-role-nazvy='<?= htmlspecialchars(json_encode($role_nazvy, JSON_UNESCAPED_UNICODE)) ?>'
        >
            <td><?= htmlspecialchars($skupina['blkt_nazev']) ?></td>
            <td><?= htmlspecialchars($skupina['blkt_popis']) ?></td>
            <td><?= $pocet_uzivatelu ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div id="group-toolbar" style="display:none;">
    <button class="btn btn-edit-user" id="edit-group-btn">Upravit</button>
    <button class="btn btn-delete-user" id="delete-group-btn">Smazat</button>
</div>

<!-- Zobrazená karta vybrané skupiny -->
<div id="group-card" class="user-card" style="display:none;">
    <div class="group-card-content">
        <input type="hidden" id="group-card-id" value="">
        <h3 id="group-card-nazev"></h3>
        <p class="group-description"><strong>Popis:</strong> <span id="group-card-popis"></span></p>

        <div class="group-roles">
            <h4>Přiřazené role:</h4>
            <ul id="group-card-role-list" class="role-list">
                <!-- Dynamicky naplněno -->
            </ul>
        </div>
    </div>
</div>

<!-- Modální okno pro Přidat/Upravit skupinu -->
<div id="blkt-group-overlay" class="blkt-modal-overlay" style="display:none;"></div>
<div id="blkt-group-modal" class="blkt-modal medium" style="display:none;">
    <div class="blkt-modal-header">
        <h3 id="blkt-group-modal-title">Přidat skupinu</h3>
        <button type="button" id="blkt-group-modal-close" class="blkt-modal-close">&times;</button>
    </div>
    <div class="blkt-modal-body">
        <form id="blkt-group-form" method="post">
            <input type="hidden" name="blkt_idskupiny" id="blkt-group-modal-id">

            <div class="blkt-formular-skupina">
                <input type="text" name="blkt_nazev" id="blkt-group-nazev" placeholder=" " required>
                <label for="blkt-group-nazev">Název skupiny</label>
            </div>

            <div class="blkt-formular-skupina">
                <textarea name="blkt_popis" id="blkt-group-popis" rows="3" placeholder=" "></textarea>
                <label for="blkt-group-popis">Popis skupiny</label>
            </div>

            <div class="blkt-formular-skupina">
                <label style="position: static; margin-bottom: 1rem; display: block; font-weight: 600;">Oprávnění skupiny:</label>
                <div class="role-checkboxes">
                    <?php foreach ($role as $r):
                        // Dekódovat JSON oprávnění
                        $opravneni = json_decode($r['blkt_obsahrole'], true) ?? [];
                        ?>
                        <label class="role-checkbox-label" for="role-<?= $r['blkt_idrole'] ?>">
                            <input type="checkbox"
                                   name="role[]"
                                   value="<?= $r['blkt_idrole'] ?>"
                                   id="role-<?= $r['blkt_idrole'] ?>"
                                   class="role-checkbox">
                            <div class="role-info">
                                <span class="role-name"><?= htmlspecialchars($r['blkt_nazev']) ?></span>
                                <?php if (!empty($r['blkt_popis'])): ?>
                                    <span class="role-description"><?= htmlspecialchars($r['blkt_popis']) ?></span>
                                <?php endif; ?>

                                <?php if (!empty($opravneni)): ?>
                                    <div class="role-permissions">
                                        <?php if (isset($opravneni['read']) && $opravneni['read']): ?>
                                            <span class="permission-badge read">Číst</span>
                                        <?php endif; ?>
                                        <?php if (isset($opravneni['write']) && $opravneni['write']): ?>
                                            <span class="permission-badge write">Upravovat</span>
                                        <?php endif; ?>
                                        <?php if (isset($opravneni['delete']) && $opravneni['delete']): ?>
                                            <span class="permission-badge delete">Mazat</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </form>
    </div>
    <div class="blkt-modal-footer">
        <button type="button" class="btn btn-cancel" id="blkt-group-cancel">Zrušit</button>
        <button type="submit" form="blkt-group-form" class="btn btn-save">Uložit</button>
    </div>
</div>

<style>
    /* Specifické styly pro skupiny */
    .group-card-content {
        width: 100%;
    }

    .group-card-content h3 {
        color: var(--blkt-primary);
        margin-bottom: 1rem;
    }

    .group-description {
        margin-bottom: 1.5rem;
        color: var(--blkt-text);
    }

    .group-roles h4 {
        color: var(--blkt-primary);
        margin-bottom: 0.5rem;
        font-size: 1.1em;
    }

    .role-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .role-list li {
        background: linear-gradient(135deg, var(--blkt-primary), var(--blkt-primary-light));
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.9em;
        transition: all 0.3s ease;
    }

    .role-list li:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    }

    /* Checkboxy pro role v modalu */
    .role-checkboxes {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid var(--blkt-border-light);
        border-radius: 8px;
        padding: 1rem;
        background: var(--blkt-glass-light);
    }

    .role-checkbox-label {
        display: flex;
        align-items: flex-start;
        padding: 0.5rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 6px;
        position: static !important;
        background: transparent;
        font-weight: normal;
        transform: none !important;
        top: auto !important;
        left: auto !important;
    }

    .role-checkbox-label:hover {
        background: rgba(52, 152, 219, 0.05);
    }

    /* Důležité - přepsat globální styly pro checkboxy */
    .role-checkbox {
        -webkit-appearance: checkbox !important;
        -moz-appearance: checkbox !important;
        appearance: checkbox !important;
        margin-right: 0.75rem;
        margin-top: 0.2rem;
        cursor: pointer;
        width: 18px !important;
        height: 18px !important;
        flex-shrink: 0;
        position: static !important;
        opacity: 1 !important;
        pointer-events: auto !important;
        display: inline-block !important;
    }

    /* Zajistit, že checkbox není skrytý */
    input[type="checkbox"].role-checkbox {
        visibility: visible !important;
        opacity: 1 !important;
        position: static !important;
        width: 18px !important;
        height: 18px !important;
    }

    .role-info {
        flex: 1;
    }

    .role-permissions {
        margin-top: 0.5rem;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .permission-badge {
        font-size: 0.75em;
        padding: 0.2rem 0.5rem;
        border-radius: 12px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .permission-badge.read {
        background: linear-gradient(135deg, #3498db, #5dade2);
        color: white;
    }

    .permission-badge.write {
        background: linear-gradient(135deg, #f39c12, #f8c471);
        color: white;
    }

    .permission-badge.delete {
        background: linear-gradient(135deg, #e74c3c, #ec7063);
        color: white;
    }

    .role-name {
        font-weight: 600;
        color: var(--blkt-text);
        margin-right: 0.5rem;
    }

    .role-description {
        font-size: 0.85em;
        color: var(--blkt-text-light);
        flex: 1;
    }

    /* Responzivita */
    @media (max-width: 768px) {
        .role-checkboxes {
            max-height: 200px;
        }

        .role-checkbox-label {
            flex-direction: column;
            align-items: flex-start;
        }

        .role-checkbox {
            margin-bottom: 0.5rem;
        }
    }
</style>