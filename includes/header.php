<?php
// includes/header.php
// Hlavička s informací o přihlášeném uživateli

// Načtení auth funkcí
require_once __DIR__ . '/auth.php';

$uzivatel = blkt_uzivatel_info();
?>

<div class="blkt-hlavicka-box">
    <div class="blkt-logo">
        <a class="blkt-logo-link" href="/"><img class="blkt-hlavicka-logo" src="/media/logo.svg" alt="Logo webu blekota.online"></a>
    </div>
    <nav class="blkt-navigace">
        <a class="blkt-tlacitko blkt-tlacitko-menu" href="/">Domů</a>
        <a class="blkt-tlacitko blkt-tlacitko-menu" href="/blog">Blog</a>
        <?php if (blkt_je_prihlasen()): ?>
            <?php if (blkt_je_admin()): ?>
                <a class="blkt-tlacitko blkt-tlacitko-menu" href="/admin">Administrace</a>
            <?php endif; ?>
            <a class="blkt-tlacitko blkt-tlacitko-smazat" href="/logout.php">Odhlásit (<?= htmlspecialchars($uzivatel['jmeno']) ?>)</a>
        <?php else: ?>
            <a class="blkt-tlacitko blkt-tlacitko-menu" href="/login.php">Přihlásit</a>
        <?php endif; ?>
    </nav>
</div>