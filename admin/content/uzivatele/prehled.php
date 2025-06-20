<?php
// admin/content/uzivatele/prehled.php
// Vlastní HTML blkt-obsah-stranky záložky „Přehled" (tabulka, toolbar, modal, karta)
?>
<div class="section-toolbar">
    <button class="btn btn-new-user" id="add-user-btn">Přidat uživatele</button>
</div>

<table id="users-table">
    <thead>
    <tr>
        <th>Jméno</th>
        <th>Příjmení</th>
        <th>E-mail</th>
        <th>Skupina</th>
        <th>Admin</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($uzivatele as $uzivatel): ?>
        <tr
                data-id="<?= $uzivatel['blkt_id'] ?>"
                data-jmeno="<?= htmlspecialchars($uzivatel['blkt_jmeno']) ?>"
                data-prijmeni="<?= htmlspecialchars($uzivatel['blkt_prijmeni']) ?>"
                data-mail="<?= htmlspecialchars($uzivatel['blkt_mail']) ?>"
                data-admin="<?= $uzivatel['blkt_admin'] ? '1' : '0' ?>"
                data-idskupiny="<?= $uzivatel['blkt_idskupiny'] ?? '' ?>"
                data-skupina="<?= htmlspecialchars($uzivatel['skupina_nazev'] ?? '') ?>"
        >
            <td><?= htmlspecialchars($uzivatel['blkt_jmeno']) ?></td>
            <td><?= htmlspecialchars($uzivatel['blkt_prijmeni']) ?></td>
            <td><?= htmlspecialchars($uzivatel['blkt_mail']) ?></td>
            <td><?= htmlspecialchars($uzivatel['skupina_nazev'] ?? 'Bez skupiny') ?></td>
            <td><?= $uzivatel['blkt_admin'] ? 'Ano' : 'Ne' ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div id="user-toolbar" style="display:none;">
    <button class="btn btn-edit-user" id="edit-user-btn">Upravit</button>
    <button class="btn btn-delete-user" id="delete-user-btn">Smazat</button>
</div>

<!-- Modální okno pro Přidat/Upravit/Smazat uživatele -->
<div id="blkt-user-overlay" class="blkt-modal-overlay" style="display:none;"></div>
<div id="blkt-user-modal" class="blkt-modal" style="display:none;">
    <div class="blkt-modal-header">
        <h3 id="blkt-modal-title"></h3>
        <button id="blkt-modal-close" class="blkt-modal-close">&times;</button>
    </div>
    <div class="blkt-modal-body">
        <form id="blkt-user-form" method="post">
            <input type="hidden" name="blkt_id" id="blkt-modal-id">
            <!-- Pole se dynamicky vkládají JS -->
        </form>
    </div>
</div>

<!-- Zobrazená karta vybraného uživatele -->
<div id="user-card" class="user-card" style="display:none;">
    <div class="user-card-left">
        <input type="hidden" id="card-id" value="">
        <p><strong>Jméno:</strong> <span id="card-jmeno"></span></p>
        <p><strong>Příjmení:</strong> <span id="card-prijmeni"></span></p>
        <p><strong>E-mail:</strong> <span id="card-mail"></span></p>
        <p><strong>Skupina:</strong> <span id="card-skupina"></span></p>
        <p><strong>Admin:</strong> <span id="card-admin-text"></span></p>
    </div>
    <div class="user-card-right">
        <img src="../media/head.png" alt="Avatar uživatele" class="user-avatar">
    </div>
</div>