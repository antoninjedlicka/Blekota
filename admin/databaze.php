<?php
// databaze.php
// Všechny funkce pro CRUD operace nad tabulkami blkt_uzivatele, blkt_konfigurace, blkt_prispevky, blkt_skupiny a blkt_role.
// Přímý přístup není povolen.
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(403);
    exit('Přímý přístup není povolen.');
}

// Načtení přístupových údajů k databázi
require_once __DIR__ . '/../databaze.php';

/* === TABULKA blkt_uzivatele === */

/**
 * Vytvoří tabulku blkt_uzivatele.
 * UPRAVENO: Přidán sloupec blkt_idskupiny
 */
function blkt_create_table_uzivatele(): void {
    $sql = "
    CREATE TABLE IF NOT EXISTS blkt_uzivatele (
      blkt_id INT AUTO_INCREMENT PRIMARY KEY,
      blkt_jmeno VARCHAR(100) NOT NULL,
      blkt_prijmeni VARCHAR(100) NOT NULL,
      blkt_mail VARCHAR(255) NOT NULL UNIQUE,
      blkt_heslo VARCHAR(255) NOT NULL,
      blkt_stav TINYINT(1) NOT NULL DEFAULT 1,
      blkt_admin TINYINT(1) NOT NULL DEFAULT 0,
      blkt_idskupiny INT DEFAULT NULL,
      FOREIGN KEY (blkt_idskupiny) REFERENCES blkt_skupiny(blkt_idskupiny) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    blkt_db_connect()->exec($sql);
}

/**
 * Smaže tabulku blkt_uzivatele.
 */
function blkt_drop_table_uzivatele(): void {
    blkt_db_connect()->exec("DROP TABLE IF EXISTS blkt_uzivatele;");
}

/**
 * Bezpečné vložení uživatele s validací
 * UPRAVENO: Přidán parametr idskupiny
 * @return int
 * @throws Exception
 */
function blkt_insert_uzivatel(array $data): int {
    // Validace vstupních dat
    if (empty($data['mail']) || !filter_var($data['mail'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Neplatná e-mailová adresa.');
    }

    if (empty($data['heslo']) || strlen($data['heslo']) < 8) {
        throw new Exception('Heslo musí mít alespoň 8 znaků.');
    }

    $pdo = blkt_db_connect();

    // Kontrola duplicity e-mailu
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM blkt_uzivatele WHERE blkt_mail = :mail");
    $stmt->execute([':mail' => $data['mail']]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Uživatel s tímto e-mailem již existuje.');
    }

    // Hashování hesla s vyšší náročností
    $hashedPassword = password_hash($data['heslo'], PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 1
    ]);

    $stmt = $pdo->prepare("
        INSERT INTO blkt_uzivatele
        (blkt_jmeno, blkt_prijmeni, blkt_mail, blkt_heslo, blkt_stav, blkt_admin, blkt_idskupiny)
        VALUES
        (:jmeno, :prijmeni, :mail, :heslo, :stav, :admin, :idskupiny)
    ");

    $stmt->execute([
        ':jmeno'      => $data['jmeno'],
        ':prijmeni'   => $data['prijmeni'],
        ':mail'       => $data['mail'],
        ':heslo'      => $hashedPassword,
        ':stav'       => isset($data['stav']) ? (int)$data['stav'] : 1,
        ':admin'      => isset($data['admin']) ? (int)$data['admin'] : 0,
        ':idskupiny'  => isset($data['idskupiny']) ? (int)$data['idskupiny'] : null,
    ]);

    return (int)$pdo->lastInsertId();
}

/**
 * Aktualizuje existujícího uživatele podle ID.
 * UPRAVENO: Přidán parametr idskupiny
 *
 * @param int   $id
 * @param array $data ['jmeno','prijmeni','mail','stav','admin','idskupiny']
 * @return bool
 */
/**
 * Aktualizuje existujícího uživatele podle ID.
 * UPRAVENO: Přidána podpora pro změnu hesla
 *
 * @param int   $id
 * @param array $data ['jmeno','prijmeni','mail','stav','admin','idskupiny','heslo']
 * @return bool
 */
function blkt_update_uzivatel(int $id, array $data): bool {
    $pdo = blkt_db_connect();

    // Základní SQL bez hesla
    $sql = "
      UPDATE blkt_uzivatele SET
        blkt_jmeno      = :jmeno,
        blkt_prijmeni   = :prijmeni,
        blkt_mail       = :mail,
        blkt_stav       = :stav,
        blkt_admin      = :admin,
        blkt_idskupiny  = :idskupiny
    ";

    $params = [
        ':jmeno'      => $data['jmeno'],
        ':prijmeni'   => $data['prijmeni'],
        ':mail'       => $data['mail'],
        ':stav'       => $data['stav'] ? 1 : 0,
        ':admin'      => $data['admin'] ? 1 : 0,
        ':idskupiny'  => isset($data['idskupiny']) ? (int)$data['idskupiny'] : null,
        ':id'         => $id,
    ];

    // Pokud je zadané nové heslo, přidáme ho do SQL
    if (!empty($data['heslo'])) {
        $sql .= ", blkt_heslo = :heslo";
        $params[':heslo'] = password_hash($data['heslo'], PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 1
        ]);
    }

    $sql .= " WHERE blkt_id = :id";

    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

/**
 * Smaže uživatele podle ID.
 *
 * @param int $id
 * @return bool
 */
function blkt_delete_uzivatel(int $id): bool {
    return blkt_db_connect()
        ->prepare("DELETE FROM blkt_uzivatele WHERE blkt_id = :id")
        ->execute([':id' => $id]);
}

/* === TABULKA blkt_skupiny === */

/**
 * Vytvoří tabulku blkt_skupiny.
 */
function blkt_create_table_skupiny(): void {
    $sql = "
    CREATE TABLE IF NOT EXISTS blkt_skupiny (
      blkt_idskupiny INT AUTO_INCREMENT PRIMARY KEY,
      blkt_nazev VARCHAR(100) NOT NULL UNIQUE,
      blkt_popis TEXT,
      blkt_role TEXT COMMENT 'Čárkami oddělená ID rolí'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    blkt_db_connect()->exec($sql);
}

/**
 * Smaže tabulku blkt_skupiny.
 */
function blkt_drop_table_skupiny(): void {
    // Nejprve musíme odstranit cizí klíč z tabulky uživatelů
    $pdo = blkt_db_connect();
    try {
        // Najdeme název cizího klíče
        $stmt = $pdo->prepare("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'blkt_uzivatele' 
            AND COLUMN_NAME = 'blkt_idskupiny' 
            AND REFERENCED_TABLE_NAME = 'blkt_skupiny'
        ");
        $stmt->execute();
        $constraint = $stmt->fetchColumn();

        if ($constraint) {
            $pdo->exec("ALTER TABLE blkt_uzivatele DROP FOREIGN KEY $constraint");
        }

        // Odstraníme sloupec
        $pdo->exec("ALTER TABLE blkt_uzivatele DROP COLUMN IF EXISTS blkt_idskupiny");
    } catch (Exception $e) {
        // Pokud se nepodaří, pokračujeme
    }

    // Smažeme tabulku
    $pdo->exec("DROP TABLE IF EXISTS blkt_skupiny;");
}

/**
 * Vloží novou skupinu.
 *
 * @param array $data ['nazev','popis','role']
 * @return int ID vložené skupiny
 * @throws Exception
 */
function blkt_insert_skupina(array $data): int {
    if (empty($data['nazev'])) {
        throw new Exception('Název skupiny je povinný.');
    }

    $pdo = blkt_db_connect();

    // Kontrola duplicity názvu
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM blkt_skupiny WHERE blkt_nazev = :nazev");
    $stmt->execute([':nazev' => $data['nazev']]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Skupina s tímto názvem již existuje.');
    }

    $stmt = $pdo->prepare("
        INSERT INTO blkt_skupiny (blkt_nazev, blkt_popis, blkt_role)
        VALUES (:nazev, :popis, :role)
    ");

    $stmt->execute([
        ':nazev' => $data['nazev'],
        ':popis' => $data['popis'] ?? '',
        ':role'  => $data['role'] ?? '',
    ]);

    return (int)$pdo->lastInsertId();
}

/**
 * Aktualizuje existující skupinu podle ID.
 *
 * @param int   $id
 * @param array $data ['nazev','popis','role']
 * @return bool
 * @throws Exception
 */
function blkt_update_skupina(int $id, array $data): bool {
    if (empty($data['nazev'])) {
        throw new Exception('Název skupiny je povinný.');
    }

    $pdo = blkt_db_connect();

    // Kontrola duplicity názvu (kromě aktuální skupiny)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM blkt_skupiny WHERE blkt_nazev = :nazev AND blkt_idskupiny != :id");
    $stmt->execute([':nazev' => $data['nazev'], ':id' => $id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Skupina s tímto názvem již existuje.');
    }

    $stmt = $pdo->prepare("
        UPDATE blkt_skupiny SET
            blkt_nazev = :nazev,
            blkt_popis = :popis,
            blkt_role = :role
        WHERE blkt_idskupiny = :id
    ");

    return $stmt->execute([
        ':nazev' => $data['nazev'],
        ':popis' => $data['popis'] ?? '',
        ':role'  => $data['role'] ?? '',
        ':id'    => $id,
    ]);
}

/**
 * Smaže skupinu podle ID.
 *
 * @param int $id
 * @return bool
 */
function blkt_delete_skupina(int $id): bool {
    // Při smazání skupiny se automaticky nastaví NULL všem uživatelům díky ON DELETE SET NULL
    return blkt_db_connect()
        ->prepare("DELETE FROM blkt_skupiny WHERE blkt_idskupiny = :id")
        ->execute([':id' => $id]);
}

/**
 * Získá všechny skupiny.
 *
 * @return array
 */
function blkt_get_skupiny(): array {
    $stmt = blkt_db_connect()->query("
        SELECT * FROM blkt_skupiny 
        ORDER BY blkt_nazev
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Získá skupinu podle ID.
 *
 * @param int $id
 * @return array|null
 */
function blkt_get_skupina(int $id): ?array {
    $stmt = blkt_db_connect()->prepare("
        SELECT * FROM blkt_skupiny 
        WHERE blkt_idskupiny = :id
    ");
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}

/**
 * Získá počet uživatelů ve skupině.
 *
 * @param int $id
 * @return int
 */
function blkt_pocet_uzivatelu_ve_skupine(int $id): int {
    $stmt = blkt_db_connect()->prepare("
        SELECT COUNT(*) FROM blkt_uzivatele 
        WHERE blkt_idskupiny = :id
    ");
    $stmt->execute([':id' => $id]);
    return (int)$stmt->fetchColumn();
}

/* === TABULKA blkt_role === */

/**
 * Vytvoří tabulku blkt_role.
 */
function blkt_create_table_role(): void {
    $sql = "
    CREATE TABLE IF NOT EXISTS blkt_role (
      blkt_idrole INT AUTO_INCREMENT PRIMARY KEY,
      blkt_nazev VARCHAR(100) NOT NULL UNIQUE,
      blkt_popis TEXT,
      blkt_obsahrole TEXT COMMENT 'JSON nebo serialized data s oprávněními'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    blkt_db_connect()->exec($sql);
}

/**
 * Smaže tabulku blkt_role.
 */
function blkt_drop_table_role(): void {
    blkt_db_connect()->exec("DROP TABLE IF EXISTS blkt_role;");
}

/**
 * Vloží novou roli.
 *
 * @param array $data ['nazev','popis','obsahrole']
 * @return int ID vložené role
 * @throws Exception
 */
function blkt_insert_role(array $data): int {
    if (empty($data['nazev'])) {
        throw new Exception('Název role je povinný.');
    }

    $pdo = blkt_db_connect();

    // Kontrola duplicity názvu
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM blkt_role WHERE blkt_nazev = :nazev");
    $stmt->execute([':nazev' => $data['nazev']]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Role s tímto názvem již existuje.');
    }

    $stmt = $pdo->prepare("
        INSERT INTO blkt_role (blkt_nazev, blkt_popis, blkt_obsahrole)
        VALUES (:nazev, :popis, :obsahrole)
    ");

    // Pokud je obsahrole pole, převedeme na JSON
    $obsahrole = $data['obsahrole'] ?? '';
    if (is_array($obsahrole)) {
        $obsahrole = json_encode($obsahrole, JSON_UNESCAPED_UNICODE);
    }

    $stmt->execute([
        ':nazev'      => $data['nazev'],
        ':popis'      => $data['popis'] ?? '',
        ':obsahrole'  => $obsahrole,
    ]);

    return (int)$pdo->lastInsertId();
}

/**
 * Aktualizuje existující roli podle ID.
 *
 * @param int   $id
 * @param array $data ['nazev','popis','obsahrole']
 * @return bool
 * @throws Exception
 */
function blkt_update_role(int $id, array $data): bool {
    if (empty($data['nazev'])) {
        throw new Exception('Název role je povinný.');
    }

    $pdo = blkt_db_connect();

    // Kontrola duplicity názvu (kromě aktuální role)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM blkt_role WHERE blkt_nazev = :nazev AND blkt_idrole != :id");
    $stmt->execute([':nazev' => $data['nazev'], ':id' => $id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Role s tímto názvem již existuje.');
    }

    // Pokud je obsahrole pole, převedeme na JSON
    $obsahrole = $data['obsahrole'] ?? '';
    if (is_array($obsahrole)) {
        $obsahrole = json_encode($obsahrole, JSON_UNESCAPED_UNICODE);
    }

    $stmt = $pdo->prepare("
        UPDATE blkt_role SET
            blkt_nazev = :nazev,
            blkt_popis = :popis,
            blkt_obsahrole = :obsahrole
        WHERE blkt_idrole = :id
    ");

    return $stmt->execute([
        ':nazev'      => $data['nazev'],
        ':popis'      => $data['popis'] ?? '',
        ':obsahrole'  => $obsahrole,
        ':id'         => $id,
    ]);
}

/**
 * Smaže roli podle ID.
 *
 * @param int $id
 * @return bool
 */
function blkt_delete_role(int $id): bool {
    return blkt_db_connect()
        ->prepare("DELETE FROM blkt_role WHERE blkt_idrole = :id")
        ->execute([':id' => $id]);
}

/**
 * Získá všechny role.
 *
 * @return array
 */
function blkt_get_role(): array {
    $stmt = blkt_db_connect()->query("
        SELECT * FROM blkt_role 
        ORDER BY blkt_nazev
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Získá roli podle ID.
 *
 * @param int $id
 * @return array|null
 */
function blkt_get_role_by_id(int $id): ?array {
    $stmt = blkt_db_connect()->prepare("
        SELECT * FROM blkt_role 
        WHERE blkt_idrole = :id
    ");
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Pokud obsahrole je JSON, dekódujeme ho
    if ($result && !empty($result['blkt_obsahrole'])) {
        $decoded = json_decode($result['blkt_obsahrole'], true);
        if ($decoded !== null) {
            $result['blkt_obsahrole'] = $decoded;
        }
    }

    return $result ?: null;
}

/**
 * Získá role podle seznamu ID.
 *
 * @param array $ids Pole ID rolí
 * @return array
 */
function blkt_get_role_by_ids(array $ids): array {
    if (empty($ids)) {
        return [];
    }

    $pdo = blkt_db_connect();
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("
        SELECT * FROM blkt_role 
        WHERE blkt_idrole IN ($placeholders)
        ORDER BY blkt_nazev
    ");
    $stmt->execute($ids);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Dekódujeme JSON v obsahrole
    foreach ($result as &$role) {
        if (!empty($role['blkt_obsahrole'])) {
            $decoded = json_decode($role['blkt_obsahrole'], true);
            if ($decoded !== null) {
                $role['blkt_obsahrole'] = $decoded;
            }
        }
    }

    return $result;
}

/**
 * Zkontroluje, zda je role použita v nějaké skupině.
 *
 * @param int $id
 * @return bool
 */
function blkt_je_role_pouzita(int $id): bool {
    $skupiny = blkt_get_skupiny();
    foreach ($skupiny as $skupina) {
        if (!empty($skupina['blkt_role'])) {
            $role_ids = array_map('trim', explode(',', $skupina['blkt_role']));
            if (in_array($id, $role_ids)) {
                return true;
            }
        }
    }
    return false;
}

/* === TABULKA blkt_konfigurace === */

/**
 * Vytvoří tabulku blkt_konfigurace.
 */
function blkt_create_table_konfigurace(): void {
    $sql = "
    CREATE TABLE IF NOT EXISTS blkt_konfigurace (
      blkt_id INT AUTO_INCREMENT PRIMARY KEY,
      blkt_nazev VARCHAR(100) NOT NULL,
      blkt_kod VARCHAR(100) NOT NULL UNIQUE,
      blkt_hodnota TEXT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    blkt_db_connect()->exec($sql);
}

/**
 * Smaže tabulku blkt_konfigurace.
 */
function blkt_drop_table_konfigurace(): void {
    blkt_db_connect()->exec("DROP TABLE IF EXISTS blkt_konfigurace;");
}

/**
 * Vloží nový záznam do konfigurace.
 *
 * @param array $data ['nazev','kod','hodnota']
 * @return int
 */
function blkt_insert_konfigurace(array $data): int {
    $stmt = blkt_db_connect()->prepare("
      INSERT INTO blkt_konfigurace (blkt_nazev, blkt_kod, blkt_hodnota)
      VALUES (:nazev, :kod, :hodnota)
    ");
    $stmt->execute([
        ':nazev'   => $data['nazev'],
        ':kod'     => $data['kod'],
        ':hodnota' => $data['hodnota'],
    ]);
    return (int)blkt_db_connect()->lastInsertId();
}

function blkt_uprav_konfiguraci_podle_kodu(string $kod, string $hodnota): bool {
    $stmt = blkt_db_connect()->prepare("
        UPDATE blkt_konfigurace 
        SET blkt_hodnota = :hodnota 
        WHERE blkt_kod = :kod
    ");
    return $stmt->execute([
        ':hodnota' => $hodnota,
        ':kod'     => $kod
    ]);
}

/* === TABULKA blkt_prispevky === */

/**
 * Vytvoří tabulku blkt_prispevky.
 */
function blkt_create_table_prispevky(): void {
    $sql = "
    CREATE TABLE IF NOT EXISTS blkt_prispevky (
      blkt_id INT AUTO_INCREMENT PRIMARY KEY,
      blkt_nazev VARCHAR(255) NOT NULL,
      blkt_kategorie VARCHAR(100) NOT NULL,
      blkt_obsah TEXT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    blkt_db_connect()->exec($sql);
}

/**
 * Smaže tabulku blkt_prispevky.
 */
function blkt_drop_table_prispevky(): void {
    blkt_db_connect()->exec("DROP TABLE IF EXISTS blkt_prispevky;");
}

/**
 * Vloží nový příspěvek.
 *
 * @param array $data ['nazev','kategorie','blkt_obsah']
 * @return int
 */
function blkt_insert_prispevek(array $data): int {
    $pdo = blkt_db_connect();
    $stmt = $pdo->prepare("
      INSERT INTO blkt_prispevky (blkt_nazev, blkt_kategorie, blkt_obsah)
      VALUES (:nazev, :kategorie, :obsah)
    ");
    $stmt->execute([
        ':nazev'     => $data['nazev'],
        ':kategorie' => $data['kategorie'],
        ':obsah'     => $data['blkt_obsah'],
    ]);
    return (int) $pdo->lastInsertId();
}

/**
 * Aktualizuje příspěvek podle ID.
 *
 * @param int   $id
 * @param array $data ['nazev','kategorie','blkt_obsah']
 * @return bool
 */
function blkt_update_prispevek(int $id, array $data): bool {
    $stmt = blkt_db_connect()->prepare("
      UPDATE blkt_prispevky SET
        blkt_nazev     = :nazev,
        blkt_kategorie = :kategorie,
        blkt_obsah     = :obsah
      WHERE blkt_id = :id
    ");
    return $stmt->execute([
        ':nazev'     => $data['nazev'],
        ':kategorie' => $data['kategorie'],
        ':obsah'     => $data['blkt_obsah'],
        ':id'        => $id,
    ]);
}

/**
 * Smaže příspěvek podle ID.
 *
 * @param int $id
 * @return bool
 */
function blkt_delete_prispevek(int $id): bool {
    return blkt_db_connect()
        ->prepare("DELETE FROM blkt_prispevky WHERE blkt_id = :id")
        ->execute([':id' => $id]);
}

/**
 * Vytvoří tabulku blkt_images.
 */
function blkt_vytvor_tabku_obrazku(): void {
    $sql = "CREATE TABLE IF NOT EXISTS blkt_images (
        blkt_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        blkt_filename VARCHAR(255) NOT NULL,
        blkt_original_name VARCHAR(255) NOT NULL,
        blkt_title VARCHAR(255) NULL,
        blkt_alt VARCHAR(255) NULL,
        blkt_description TEXT NULL,
        blkt_created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (blkt_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    blkt_db_connect()->exec($sql);
}

/**
 * Vloží nový obrázek do databáze a nahraje soubor
 * NOVÁ VERZE: Automaticky konvertuje všechny obrázky na WebP
 */
function blkt_vloz_obrazek(array $file, string $title = '', string $alt = '', string $description = ''): int {
    $pdo = blkt_db_connect();

    // Kontrola chyb při nahrávání
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'Soubor je příliš velký (překročen limit php.ini).',
            UPLOAD_ERR_FORM_SIZE => 'Soubor je příliš velký (překročen limit formuláře).',
            UPLOAD_ERR_PARTIAL => 'Soubor byl nahrán pouze částečně.',
            UPLOAD_ERR_NO_FILE => 'Nebyl nahrán žádný soubor.',
            UPLOAD_ERR_NO_TMP_DIR => 'Chybí dočasná složka.',
            UPLOAD_ERR_CANT_WRITE => 'Nelze zapsat soubor na disk.',
            UPLOAD_ERR_EXTENSION => 'Nahrávání bylo zastaveno rozšířením PHP.'
        ];
        $errorMsg = $errors[$file['error']] ?? 'Neznámá chyba při nahrávání.';
        throw new Exception($errorMsg);
    }

    // Kontrola velikosti (max 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        throw new Exception('Soubor je příliš velký. Maximální velikost je 5MB.');
    }

    // Kontrola MIME typu pomocí finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg',
        'image/svg' => 'svg',
        'text/xml' => 'svg',
        'application/svg+xml' => 'svg',
        'application/xml' => 'svg'
    ];

    if (!isset($allowedMimes[$mimeType])) {
        throw new Exception('Nepodporovaný formát obrázku. Povolené jsou pouze JPG, PNG, GIF, WebP a SVG.');
    }

    // SVG necháme tak jak jsou (nekonvertujeme na WebP)
    $isSvg = in_array($mimeType, ['image/svg+xml', 'image/svg', 'text/xml', 'application/svg+xml', 'application/xml']);

    if (!$isSvg) {
        // Další kontrola - ověření, že jde skutečně o obrázek
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            throw new Exception('Nahraný soubor není platný obrázek.');
        }

        // Kontrola rozměrů obrázku
        $maxWidth = 4000;
        $maxHeight = 4000;
        if ($imageInfo[0] > $maxWidth || $imageInfo[1] > $maxHeight) {
            throw new Exception("Obrázek je příliš velký. Maximální rozměry jsou {$maxWidth}x{$maxHeight}px.");
        }
    } else {
        // Pro SVG ověříme základní strukturu
        $svgContent = file_get_contents($file['tmp_name']);
        if (strpos($svgContent, '<svg') === false && strpos($svgContent, '<?xml') === false) {
            throw new Exception('Nahraný soubor není platný SVG.');
        }

        // Bezpečnostní kontrola SVG obsahu
        $dangerousPatterns = [
            '/<script/i',
            '/on\w+\s*=/i', // onclick, onload, etc.
            '/<iframe/i',
            '/<embed/i',
            '/<object/i',
            '/javascript:/i'
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $svgContent)) {
                throw new Exception('SVG soubor obsahuje nebezpečný obsah.');
            }
        }
    }

    // Cesty
    $baseDir = dirname(__DIR__) . '/media/upload';
    $year = date('Y');
    $month = date('m');
    $yearDir = "$baseDir/$year";
    $monthDir = "$yearDir/$month";

    // Vytvoření složek s bezpečnými právy
    if (!is_dir($yearDir)) {
        if (!mkdir($yearDir, 0755, true) && !is_dir($yearDir)) {
            throw new Exception("Nepodařilo se vytvořit složku pro rok.");
        }
    }

    if (!is_dir($monthDir)) {
        if (!mkdir($monthDir, 0755, true) && !is_dir($monthDir)) {
            throw new Exception("Nepodařilo se vytvořit složku pro měsíc.");
        }
    }

    // Vytvoření .htaccess pro zabezpečení
    $htaccessContent = "Options -Indexes\n";
    $htaccessContent .= "Options -ExecCGI\n";
    $htaccessContent .= "AddHandler cgi-script .php .pl .py .jsp .asp .sh .cgi\n";
    $htaccessContent .= "<FilesMatch \"\\.(php|pl|py|jsp|asp|sh|cgi)$\">\n";
    $htaccessContent .= "    Order Deny,Allow\n";
    $htaccessContent .= "    Deny from all\n";
    $htaccessContent .= "</FilesMatch>\n";

    file_put_contents("$monthDir/.htaccess", $htaccessContent);

    if (!is_writable($monthDir)) {
        throw new Exception("Cílová složka není zapisovatelná.");
    }

    // Pro SVG použijeme původní příponu, pro ostatní WebP
    $ext = $isSvg ? $allowedMimes[$mimeType] : 'webp';

    // Generování bezpečného názvu souboru
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = $monthDir . '/' . $filename;

    // Kontrola, zda soubor již neexistuje
    while (file_exists($dest)) {
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest = $monthDir . '/' . $filename;
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        throw new Exception("Neplatný dočasný soubor.");
    }

    // Pro SVG jen přesuneme soubor
    if ($isSvg) {
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new Exception("Nepodařilo se uložit soubor.");
        }
    } else {
        // Pro ostatní obrázky provedeme konverzi na WebP
        if (!blkt_konvertuj_na_webp($file['tmp_name'], $dest, $mimeType)) {
            throw new Exception("Nepodařilo se převést obrázek na WebP formát.");
        }
    }

    // Nastavení správných práv
    chmod($dest, 0644);

    // Sanitizace vstupních dat
    $title = substr(trim($title), 0, 255);
    $alt = substr(trim($alt), 0, 255);
    $description = trim($description);

    // DB záznam
    $stmt = $pdo->prepare("
        INSERT INTO blkt_images
            (blkt_filename, blkt_original_name, blkt_title, blkt_alt, blkt_description)
        VALUES
            (:filename, :orig, :title, :alt, :desc)
    ");

    $stmt->execute([
        ':filename' => "$year/$month/$filename",
        ':orig'     => basename($file['name']), // Pouze název souboru, bez cesty
        ':title'    => $title,
        ':alt'      => $alt,
        ':desc'     => $description
    ]);

    return (int)$pdo->lastInsertId();
}

/**
 * Konvertuje obrázek na WebP formát s optimalizací
 * @param string $source Cesta ke zdrojovému souboru
 * @param string $destination Cílová cesta pro WebP
 * @param string $mimeType MIME typ zdrojového souboru
 * @return bool
 */
function blkt_konvertuj_na_webp(string $source, string $destination, string $mimeType): bool {
    // Načtení obrázku podle typu
    switch ($mimeType) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        case 'image/webp':
            // Pokud je už WebP, jen zkopírujeme
            return copy($source, $destination);
        default:
            return false;
    }

    if (!$image) {
        return false;
    }

    // Získání rozměrů
    $width = imagesx($image);
    $height = imagesy($image);

    // Pokud je obrázek větší než 1920px, zmenšíme ho
    $maxDimension = 1920;
    if ($width > $maxDimension || $height > $maxDimension) {
        $ratio = min($maxDimension / $width, $maxDimension / $height);
        $newWidth = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);

        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Zachování průhlednosti pro PNG
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagepalettetotruecolor($image);
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);
        $image = $newImage;
    } else {
        // I když nezmenšujeme, zajistíme správnou práci s průhledností
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagepalettetotruecolor($image);
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }
    }

    // Uložení jako WebP
    // Kvalita 85 je dobrý kompromis mezi velikostí a kvalitou
    $quality = 85;
    $result = imagewebp($image, $destination, $quality);

    imagedestroy($image);

    return $result;
}

/**
 * Aktualizuje metadata obrázku a volitelně nahradí soubor
 * UPRAVENÁ VERZE: Podporuje konverzi na WebP při aktualizaci
 */
function blkt_uprav_obrazek(int $id, string $title = '', string $alt = '', string $description = '', ?array $file = null): bool {
    $pdo = blkt_db_connect();
    $sqlFile = '';
    $params = [
        ':title' => substr(trim($title), 0, 255),
        ':alt'   => substr(trim($alt), 0, 255),
        ':desc'  => trim($description),
        ':id'    => $id
    ];

    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        // Získáme starý soubor pro smazání
        $stmt0 = $pdo->prepare("SELECT blkt_filename FROM blkt_images WHERE blkt_id = :id");
        $stmt0->execute([':id' => $id]);
        $old = $stmt0->fetchColumn();

        // Kontrola MIME typu
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'image/svg' => 'svg',
            'text/xml' => 'svg',
            'application/svg+xml' => 'svg',
            'application/xml' => 'svg'
        ];

        if (!isset($allowedMimes[$mimeType])) {
            throw new Exception('Nepodporovaný formát obrázku.');
        }

        $isSvg = in_array($mimeType, ['image/svg+xml', 'image/svg', 'text/xml', 'application/svg+xml', 'application/xml']);
        $ext = $isSvg ? $allowedMimes[$mimeType] : 'webp';

        // Cesty
        $baseDir = dirname(__DIR__) . '/media/upload';
        $year = date('Y');
        $month = date('m');
        $yearDir = "$baseDir/$year";
        $monthDir = "$yearDir/$month";

        if (!is_dir($yearDir)) {
            mkdir($yearDir, 0755, true);
        }
        if (!is_dir($monthDir)) {
            mkdir($monthDir, 0755, true);
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest = $monthDir . '/' . $filename;

        if ($isSvg) {
            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                throw new Exception('Nepodařilo se uložit nový soubor.');
            }
        } else {
            if (!blkt_konvertuj_na_webp($file['tmp_name'], $dest, $mimeType)) {
                throw new Exception('Nepodařilo se převést obrázek na WebP formát.');
            }
        }

        chmod($dest, 0644);

        // Smažeme starý soubor
        if ($old) {
            $oldPath = $baseDir . '/' . $old;
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $sqlFile = ", blkt_filename = :filename, blkt_original_name = :orig";
        $params[':filename'] = "$year/$month/$filename";
        $params[':orig']     = basename($file['name']);
    }

    $sql = "
        UPDATE blkt_images
        SET blkt_title = :title,
            blkt_alt = :alt,
            blkt_description = :desc
            $sqlFile
        WHERE blkt_id = :id
    ";

    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

/**
 * Původní funkce optimalizace obrázku - již není potřeba, ale ponecháváme pro zpětnou kompatibilitu
 * @deprecated Použijte blkt_konvertuj_na_webp místo této funkce
 */
function blkt_optimalizuj_obrazek(string $path, string $mimeType): void {
    // Tato funkce už není potřeba, protože optimalizaci provádíme přímo při konverzi na WebP
    return;
}

/**
 * Smaže obrázek z databáze a zároveň odstraní fyzický soubor.
 *
 * @param int $id
 * @return bool
 */
function blkt_smaz_obrazek(int $id): bool {
    $pdo = blkt_db_connect();

    $stmt0 = $pdo->prepare("SELECT blkt_filename FROM blkt_images WHERE blkt_id = :id");
    $stmt0->execute([':id' => $id]);
    $filename = $stmt0->fetchColumn();

    if ($filename) {
        $filePath = dirname(__DIR__) . '/media/upload/' . $filename;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM blkt_images WHERE blkt_id = :id");
    return $stmt->execute([':id' => $id]);
}

/**
 * Vytvoří tabulku blkt_obsah_detaily.
 */
function blkt_create_table_obsah_detaily(): void {
    $sql = "
    CREATE TABLE IF NOT EXISTS blkt_obsah_detaily (
      blkt_id INT AUTO_INCREMENT PRIMARY KEY,
      blkt_parent_id INT NOT NULL,
      blkt_type VARCHAR(20) NOT NULL,          -- 'post', 'page', apod.
      blkt_slug VARCHAR(255) NOT NULL UNIQUE,
      blkt_tags TEXT NULL,                     -- CSV seznam štítků
      INDEX (blkt_parent_id),
      INDEX (blkt_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    blkt_db_connect()->exec($sql);
}

/**
 * Smaže tabulku blkt_obsah_detaily.
 */
function blkt_drop_table_obsah_detaily(): void {
    blkt_db_connect()->exec("DROP TABLE IF EXISTS blkt_obsah_detaily;");
}

/**
 * Vloží záznam do blkt_obsah_detaily.
 *
 * @param int    $parentId ID příspěvku/stránky
 * @param string $type     'post'|'page'|...
 * @param string $slug
 * @param string $tags     CSV seznam štítků
 * @return int ID vloženého záznamu
 */
function blkt_insert_obsah_detail(int $parentId, string $type, string $slug, string $tags = ''): int {
    $pdo = blkt_db_connect();
    $stmt = $pdo->prepare("
      INSERT INTO blkt_obsah_detaily
        (blkt_parent_id, blkt_type, blkt_slug, blkt_tags)
      VALUES
        (:pid, :type, :slug, :tags)
    ");
    $stmt->execute([
        ':pid'  => $parentId,
        ':type' => $type,
        ':slug' => $slug,
        ':tags' => $tags
    ]);
    return (int)$pdo->lastInsertId();
}

/**
 * Aktualizuje detail pro daný parent_id+type.
 *
 * @param int    $parentId
 * @param string $type
 * @param string $slug
 * @param string $tags
 * @return bool
 */
function blkt_update_obsah_detail(int $parentId, string $type, string $slug, string $tags = ''): bool {
    $stmt = blkt_db_connect()->prepare("
      UPDATE blkt_obsah_detaily SET
        blkt_slug = :slug,
        blkt_tags = :tags
      WHERE blkt_parent_id = :pid AND blkt_type = :type
    ");
    return $stmt->execute([
        ':slug'  => $slug,
        ':tags'  => $tags,
        ':pid'   => $parentId,
        ':type'  => $type
    ]);
}

/**
 * Smaže detail u daného parent_id+type.
 *
 * @param int    $parentId
 * @param string $type
 * @return bool
 */
function blkt_delete_obsah_detail(int $parentId, string $type): bool {
    $stmt = blkt_db_connect()->prepare("
      DELETE FROM blkt_obsah_detaily
      WHERE blkt_parent_id = :pid AND blkt_type = :type
    ");
    return $stmt->execute([
        ':pid'  => $parentId,
        ':type' => $type
    ]);
}

/**
 * Vrátí asociativní pole detailů pro daný parent_id+type.
 *
 * @param int    $parentId
 * @param string $type
 * @return array|null ['slug'=>..., 'tags'=>...] nebo null
 */
function blkt_get_obsah_detail(int $parentId, string $type): ?array {
    $stmt = blkt_db_connect()->prepare("
      SELECT blkt_slug, blkt_tags
      FROM blkt_obsah_detaily
      WHERE blkt_parent_id = :pid AND blkt_type = :type
      LIMIT 1
    ");
    $stmt->execute([
        ':pid'  => $parentId,
        ':type' => $type
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/**
 * Vrátí seznam všech unikátních štítků (pro datalist).
 *
 * @return string[] pole štítků
 */
function blkt_get_all_tags(): array {
    $pdo = blkt_db_connect();
    $all = $pdo->query("SELECT blkt_tags FROM blkt_obsah_detaily")->fetchAll(PDO::FETCH_COLUMN);
    $tags = [];
    foreach ($all as $csv) {
        foreach (array_map('trim', explode(',', $csv)) as $t) {
            if ($t !== '' && !in_array($t, $tags, true)) {
                $tags[] = $t;
            }
        }
    }
    sort($tags);
    return $tags;
}

/* === TABULKA blkt_zivotopis === */

/**
 * Vytvoří tabulku blkt_zivotopis.
 */
function blkt_create_table_zivotopis(): void {
    $sql = "
    CREATE TABLE IF NOT EXISTS blkt_zivotopis (
        blkt_id INT AUTO_INCREMENT PRIMARY KEY,
        blkt_typ VARCHAR(50) NOT NULL,
        blkt_poradi INT DEFAULT 0,
        blkt_nazev VARCHAR(255),
        blkt_podnazev VARCHAR(255),
        blkt_datum_od VARCHAR(50),
        blkt_datum_do VARCHAR(50),
        blkt_popis TEXT,
        blkt_obsah TEXT,
        blkt_tagy TEXT,
        blkt_ikona VARCHAR(50),
        blkt_uroven VARCHAR(50),
        blkt_stav TINYINT DEFAULT 1,
        INDEX idx_typ (blkt_typ),
        INDEX idx_poradi (blkt_poradi)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    blkt_db_connect()->exec($sql);
}

/**
 * Smaže tabulku blkt_zivotopis.
 */
function blkt_drop_table_zivotopis(): void {
    blkt_db_connect()->exec("DROP TABLE IF EXISTS blkt_zivotopis;");
}

/**
 * Vloží novou položku životopisu.
 *
 * @param array $data pole s daty
 * @return int ID vloženého záznamu
 */
function blkt_insert_zivotopis_polozka(array $data): int {
    $pdo = blkt_db_connect();
    $stmt = $pdo->prepare("
        INSERT INTO blkt_zivotopis
        (blkt_typ, blkt_poradi, blkt_nazev, blkt_podnazev, blkt_datum_od, 
         blkt_datum_do, blkt_popis, blkt_obsah, blkt_tagy, blkt_ikona, 
         blkt_uroven, blkt_stav)
        VALUES
        (:typ, :poradi, :nazev, :podnazev, :datum_od, 
         :datum_do, :popis, :obsah, :tagy, :ikona, 
         :uroven, :stav)
    ");

    $stmt->execute([
        ':typ'       => $data['typ'],
        ':poradi'    => $data['poradi'] ?? 0,
        ':nazev'     => $data['nazev'] ?? null,
        ':podnazev'  => $data['podnazev'] ?? null,
        ':datum_od'  => $data['datum_od'] ?? null,
        ':datum_do'  => $data['datum_do'] ?? null,
        ':popis'     => $data['popis'] ?? null,
        ':obsah'     => $data['obsah'] ?? null,
        ':tagy'      => $data['tagy'] ?? null,
        ':ikona'     => $data['ikona'] ?? null,
        ':uroven'    => $data['uroven'] ?? null,
        ':stav'      => $data['stav'] ?? 1
    ]);

    return (int)$pdo->lastInsertId();
}

/**
 * Aktualizuje položku životopisu podle ID.
 *
 * @param int   $id
 * @param array $data
 * @return bool
 */
function blkt_update_zivotopis_polozka(int $id, array $data): bool {
    $stmt = blkt_db_connect()->prepare("
        UPDATE blkt_zivotopis SET
            blkt_typ = :typ,
            blkt_poradi = :poradi,
            blkt_nazev = :nazev,
            blkt_podnazev = :podnazev,
            blkt_datum_od = :datum_od,
            blkt_datum_do = :datum_do,
            blkt_popis = :popis,
            blkt_obsah = :obsah,
            blkt_tagy = :tagy,
            blkt_ikona = :ikona,
            blkt_uroven = :uroven,
            blkt_stav = :stav
        WHERE blkt_id = :id
    ");

    return $stmt->execute([
        ':id'        => $id,
        ':typ'       => $data['typ'],
        ':poradi'    => $data['poradi'] ?? 0,
        ':nazev'     => $data['nazev'] ?? null,
        ':podnazev'  => $data['podnazev'] ?? null,
        ':datum_od'  => $data['datum_od'] ?? null,
        ':datum_do'  => $data['datum_do'] ?? null,
        ':popis'     => $data['popis'] ?? null,
        ':obsah'     => $data['obsah'] ?? null,
        ':tagy'      => $data['tagy'] ?? null,
        ':ikona'     => $data['ikona'] ?? null,
        ':uroven'    => $data['uroven'] ?? null,
        ':stav'      => $data['stav'] ?? 1
    ]);
}

/**
 * Smaže položku životopisu podle ID.
 *
 * @param int $id
 * @return bool
 */
function blkt_delete_zivotopis_polozka(int $id): bool {
    return blkt_db_connect()
        ->prepare("DELETE FROM blkt_zivotopis WHERE blkt_id = :id")
        ->execute([':id' => $id]);
}

/**
 * Načte všechny položky životopisu podle typu.
 *
 * @param string $typ Typ položky nebo 'vse' pro všechny
 * @return array
 */
function blkt_get_zivotopis_polozky(string $typ = 'vse'): array {
    $pdo = blkt_db_connect();

    if ($typ === 'vse') {
        $stmt = $pdo->prepare("
            SELECT * FROM blkt_zivotopis 
            WHERE blkt_stav = 1 
            ORDER BY blkt_typ, blkt_poradi, blkt_id
        ");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("
            SELECT * FROM blkt_zivotopis 
            WHERE blkt_typ = :typ AND blkt_stav = 1 
            ORDER BY blkt_poradi, blkt_id
        ");
        $stmt->execute([':typ' => $typ]);
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Načte jednu položku životopisu podle ID.
 *
 * @param int $id
 * @return array|null
 */
function blkt_get_zivotopis_polozka(int $id): ?array {
    $stmt = blkt_db_connect()->prepare("
        SELECT * FROM blkt_zivotopis WHERE blkt_id = :id LIMIT 1
    ");
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}

/**
 * Smaže všechny položky daného typu.
 *
 * @param string $typ
 * @return bool
 */
function blkt_delete_zivotopis_typ(string $typ): bool {
    return blkt_db_connect()
        ->prepare("DELETE FROM blkt_zivotopis WHERE blkt_typ = :typ")
        ->execute([':typ' => $typ]);
}

// Přidání indexů pro lepší výkon (spustit jednou při instalaci)
function blkt_create_indexes(): void {
    $pdo = blkt_db_connect();

    // Index pro rychlé vyhledávání uživatelů
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_mail ON blkt_uzivatele(blkt_mail)");

    // Index pro skupiny uživatelů
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_idskupiny ON blkt_uzivatele(blkt_idskupiny)");

    // Index pro konfigurace
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_kod ON blkt_konfigurace(blkt_kod)");

    // Index pro příspěvky
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_kategorie ON blkt_prispevky(blkt_kategorie)");

    // Index pro detaily obsahu
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_slug ON blkt_obsah_detaily(blkt_slug)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_parent_type ON blkt_obsah_detaily(blkt_parent_id, blkt_type)");

    // Index pro obrázky
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_created ON blkt_images(blkt_created_at DESC)");
}
?>