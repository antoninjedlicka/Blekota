<?php
// includes/auth.php - Centralizovaná kontrola přihlášení

// Načtení centrální session konfigurace
require_once __DIR__ . '/session.php';

/**
 * Kontrola, zda je uživatel přihlášen
 * @return bool
 */
function blkt_je_prihlasen(): bool {
    return isset($_SESSION['blkt_prihlasen']) && $_SESSION['blkt_prihlasen'] === true;
}

/**
 * Kontrola, zda je uživatel admin
 * @return bool
 */
function blkt_je_admin(): bool {
    return blkt_je_prihlasen() && isset($_SESSION['blkt_uzivatel_admin']) && $_SESSION['blkt_uzivatel_admin'] == 1;
}

/**
 * Získání informací o přihlášeném uživateli
 * @return array|null
 */
function blkt_uzivatel_info(): ?array {
    if (!blkt_je_prihlasen()) {
        return null;
    }

    return [
        'jmeno' => $_SESSION['blkt_uzivatel_jmeno'] ?? '',
        'prijmeni' => $_SESSION['blkt_uzivatel_prijmeni'] ?? '',
        'mail' => $_SESSION['blkt_uzivatel_mail'] ?? '',
        'admin' => $_SESSION['blkt_uzivatel_admin'] ?? 0
    ];
}

/**
 * Přesměrování na login s návratem
 * @param string $target_url Cílová URL po přihlášení
 */
function blkt_presmeruj_na_login(string $target_url = ''): void {
    if (empty($target_url)) {
        $target_url = $_SERVER['REQUEST_URI'];
    }

    $_SESSION['blkt_return_url'] = $target_url;
    header('Location: /login.php');
    exit;
}

/**
 * Vyžaduje přihlášení - pokud není, přesměruje na login
 */
function blkt_vyzaduj_prihlaseni(): void {
    if (!blkt_je_prihlasen()) {
        blkt_presmeruj_na_login();
    }
}

/**
 * Vyžaduje admin oprávnění
 */
function blkt_vyzaduj_admina(): void {
    if (!blkt_je_admin()) {
        header('Location: /');
        exit;
    }
}