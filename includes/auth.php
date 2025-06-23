<?php
// includes/auth.php - Centralizovaná kontrola přihlášení a oprávnění

// Načtení centrální session konfigurace
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../admin/databaze.php';

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
 * Získání ID přihlášeného uživatele
 * @return int|null
 */
function blkt_uzivatel_id(): ?int {
    if (!blkt_je_prihlasen()) {
        return null;
    }
    return isset($_SESSION['blkt_uzivatel_id']) ? (int)$_SESSION['blkt_uzivatel_id'] : null;
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
        'id' => $_SESSION['blkt_uzivatel_id'] ?? 0,
        'jmeno' => $_SESSION['blkt_uzivatel_jmeno'] ?? '',
        'prijmeni' => $_SESSION['blkt_uzivatel_prijmeni'] ?? '',
        'mail' => $_SESSION['blkt_uzivatel_mail'] ?? '',
        'admin' => $_SESSION['blkt_uzivatel_admin'] ?? 0,
        'idskupiny' => $_SESSION['blkt_uzivatel_idskupiny'] ?? null
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

/**
 * Zkontroluje, zda má uživatel oprávnění k dané sekci a akci
 *
 * @param int|null $uzivatel_id ID uživatele (null = aktuální uživatel)
 * @param string $sekce Název sekce (např. 'prispevky', 'uzivatele')
 * @param string $akce Typ akce ('read', 'write', 'delete')
 * @return bool
 */
function blkt_ma_uzivatel_opravneni(?int $uzivatel_id, string $sekce, string $akce = 'read'): bool {
    // Pokud není zadáno ID, použijeme aktuálního uživatele
    if ($uzivatel_id === null) {
        $uzivatel_id = blkt_uzivatel_id();
    }

    if (!$uzivatel_id) return false;

    $pdo = blkt_db_connect();

    // Nejprve zkontrolujeme, zda je uživatel admin
    $stmt = $pdo->prepare("SELECT blkt_admin, blkt_idskupiny FROM blkt_uzivatele WHERE blkt_id = :id");
    $stmt->execute([':id' => $uzivatel_id]);
    $uzivatel = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$uzivatel) return false;

    // Admin má vždy všechna práva
    if ($uzivatel['blkt_admin'] == 1) return true;

    // Pokud uživatel není ve skupině, nemá oprávnění
    if (!$uzivatel['blkt_idskupiny']) return false;

    // Získat skupinu uživatele
    $skupina = blkt_get_skupina($uzivatel['blkt_idskupiny']);
    if (!$skupina || empty($skupina['blkt_role'])) return false;

    // Získat role skupiny
    $role_ids = array_map('trim', explode(',', $skupina['blkt_role']));
    $role = blkt_get_role_by_ids($role_ids);

    // Kontrola oprávnění v rolích
    foreach ($role as $r) {
        if (!empty($r['blkt_obsahrole'])) {
            $opravneni = is_array($r['blkt_obsahrole'])
                ? $r['blkt_obsahrole']
                : json_decode($r['blkt_obsahrole'], true);

            if (!$opravneni) continue;

            // Kontrola, zda role obsahuje danou sekci
            if (isset($opravneni['sekce'])) {
                $povolene_sekce = is_array($opravneni['sekce'])
                    ? $opravneni['sekce']
                    : [$opravneni['sekce']];

                if (in_array($sekce, $povolene_sekce) || in_array('all', $povolene_sekce)) {
                    // Kontrola konkrétní akce
                    if (isset($opravneni[$akce]) && $opravneni[$akce]) {
                        return true;
                    }
                }
            }
        }
    }

    return false;
}

/**
 * Získá všechna oprávnění uživatele
 *
 * @param int|null $uzivatel_id ID uživatele (null = aktuální uživatel)
 * @return array
 */
function blkt_ziskej_opravneni_uzivatele(?int $uzivatel_id = null): array {
    // Pokud není zadáno ID, použijeme aktuálního uživatele
    if ($uzivatel_id === null) {
        $uzivatel_id = blkt_uzivatel_id();
    }

    if (!$uzivatel_id) return [];

    $pdo = blkt_db_connect();

    $stmt = $pdo->prepare("SELECT blkt_admin, blkt_idskupiny FROM blkt_uzivatele WHERE blkt_id = :id");
    $stmt->execute([':id' => $uzivatel_id]);
    $uzivatel = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$uzivatel) return [];

    // Admin má všechna práva
    if ($uzivatel['blkt_admin'] == 1) {
        return [
            'je_admin' => true,
            'sekce' => ['all'],
            'opravneni' => ['read' => true, 'write' => true, 'delete' => true]
        ];
    }

    if (!$uzivatel['blkt_idskupiny']) return [];

    // Získat všechna oprávnění ze skupiny
    $skupina = blkt_get_skupina($uzivatel['blkt_idskupiny']);
    if (!$skupina || empty($skupina['blkt_role'])) return [];

    $role_ids = array_map('trim', explode(',', $skupina['blkt_role']));
    $role = blkt_get_role_by_ids($role_ids);

    $vsechna_opravneni = [
        'je_admin' => false,
        'skupina' => $skupina['blkt_nazev'],
        'sekce' => [],
        'detailni_opravneni' => []
    ];

    foreach ($role as $r) {
        if (!empty($r['blkt_obsahrole'])) {
            $opravneni = is_array($r['blkt_obsahrole'])
                ? $r['blkt_obsahrole']
                : json_decode($r['blkt_obsahrole'], true);

            if (!$opravneni) continue;

            // Přidat sekce
            if (isset($opravneni['sekce'])) {
                $sekce = is_array($opravneni['sekce'])
                    ? $opravneni['sekce']
                    : [$opravneni['sekce']];

                foreach ($sekce as $s) {
                    if (!in_array($s, $vsechna_opravneni['sekce'])) {
                        $vsechna_opravneni['sekce'][] = $s;
                    }

                    // Přidat detailní oprávnění pro sekci
                    if (!isset($vsechna_opravneni['detailni_opravneni'][$s])) {
                        $vsechna_opravneni['detailni_opravneni'][$s] = [
                            'read' => false,
                            'write' => false,
                            'delete' => false
                        ];
                    }

                    // Aktualizovat oprávnění (OR logika - pokud má alespoň v jedné roli, má celkově)
                    if (isset($opravneni['read']) && $opravneni['read']) {
                        $vsechna_opravneni['detailni_opravneni'][$s]['read'] = true;
                    }
                    if (isset($opravneni['write']) && $opravneni['write']) {
                        $vsechna_opravneni['detailni_opravneni'][$s]['write'] = true;
                    }
                    if (isset($opravneni['delete']) && $opravneni['delete']) {
                        $vsechna_opravneni['detailni_opravneni'][$s]['delete'] = true;
                    }
                }
            }
        }
    }

    return $vsechna_opravneni;
}

/**
 * Získá seznam sekcí, ke kterým má uživatel přístup
 *
 * @param int|null $uzivatel_id ID uživatele (null = aktuální uživatel)
 * @return array
 */
function blkt_ziskej_povolene_sekce(?int $uzivatel_id = null): array {
    $opravneni = blkt_ziskej_opravneni_uzivatele($uzivatel_id);

    if (isset($opravneni['je_admin']) && $opravneni['je_admin']) {
        // Admin má přístup ke všem sekcím
        return ['all'];
    }

    return $opravneni['sekce'] ?? [];
}

/**
 * Vyžaduje konkrétní oprávnění - pokud ho nemá, přesměruje
 *
 * @param string $sekce
 * @param string $akce
 * @param string $redirect_url URL pro přesměrování při nedostatečných právech
 */
function blkt_vyzaduj_opravneni(string $sekce, string $akce = 'read', string $redirect_url = '/'): void {
    if (!blkt_ma_uzivatel_opravneni(null, $sekce, $akce)) {
        $_SESSION['blkt_chyba_opravneni'] = 'Nemáte dostatečná oprávnění pro tuto akci.';
        header('Location: ' . $redirect_url);
        exit;
    }
}

/**
 * Vrací chybovou zprávu o oprávnění a vymaže ji ze session
 *
 * @return string|null
 */
function blkt_chyba_opravneni(): ?string {
    if (isset($_SESSION['blkt_chyba_opravneni'])) {
        $chyba = $_SESSION['blkt_chyba_opravneni'];
        unset($_SESSION['blkt_chyba_opravneni']);
        return $chyba;
    }
    return null;
}