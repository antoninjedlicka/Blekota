<?php
// databaze.php
// Všechny funkce pro CRUD operace nad tabulkami blkt_uzivatele, blkt_konfigurace a blkt_prispevky.
// Přímý přístup není povolen.
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(403);
    exit('Přímý přístup není povolen.');
}

// Načtení přístupových údajů k databázi
if (!file_exists(__DIR__ . '/../connect.php')) {
    die('Konfigurační soubor connect.php nenalezen. Spusť instalátor.');
}
require_once __DIR__ . '/../connect.php';

/**
 * Vrátí PDO instanci pro práci s databází.
 *
 * @return PDO
 */
function blkt_db_connect(): PDO {
    $dsn = 'mysql:host=' . BLKT_DB_HOST . ';dbname=' . BLKT_DB_NAME . ';charset=utf8mb4';
    try {
        return new PDO(
            $dsn,
            BLKT_DB_USER,
            BLKT_DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    } catch (PDOException $e) {
        die('Chyba připojení k databázi: ' . htmlspecialchars($e->getMessage()));
    }
}

/* === TABULKA blkt_uzivatele === */

/**
 * Vytvoří tabulku blkt_uzivatele.
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
      blkt_admin TINYINT(1) NOT NULL DEFAULT 0
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
 * Vloží nového uživatele.
 *
 * @param array $data ['jmeno','prijmeni','mail','heslo','stav','admin']
 * @return int ID vloženého záznamu
 */
function blkt_insert_uzivatel(array $data): int {
    $pdo = blkt_db_connect();
    $stmt = $pdo->prepare("
      INSERT INTO blkt_uzivatele
        (blkt_jmeno, blkt_prijmeni, blkt_mail, blkt_heslo, blkt_stav, blkt_admin)
      VALUES
        (:jmeno, :prijmeni, :mail, :heslo, :stav, :admin)
    ");
    $stmt->execute([
        ':jmeno'    => $data['jmeno'],
        ':prijmeni' => $data['prijmeni'],
        ':mail'     => $data['mail'],
        ':heslo'    => password_hash($data['heslo'], PASSWORD_DEFAULT),
        ':stav'     => $data['stav'] ? 1 : 0,
        ':admin'    => $data['admin'] ? 1 : 0,
    ]);
    return (int)$pdo->lastInsertId();
}

/**
 * Aktualizuje existujícího uživatele podle ID.
 *
 * @param int   $id
 * @param array $data ['jmeno','prijmeni','mail','stav','admin']
 * @return bool
 */
function blkt_update_uzivatel(int $id, array $data): bool {
    $stmt = blkt_db_connect()->prepare("
      UPDATE blkt_uzivatele SET
        blkt_jmeno    = :jmeno,
        blkt_prijmeni = :prijmeni,
        blkt_mail     = :mail,
        blkt_stav     = :stav,
        blkt_admin    = :admin
      WHERE blkt_id = :id
    ");
    return $stmt->execute([
        ':jmeno'    => $data['jmeno'],
        ':prijmeni' => $data['prijmeni'],
        ':mail'     => $data['mail'],
        ':stav'     => $data['stav'] ? 1 : 0,
        ':admin'    => $data['admin'] ? 1 : 0,
        ':id'       => $id,
    ]);
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

    // Získání správné přípony podle MIME typu
    $ext = $allowedMimes[$mimeType];

    // Další kontrola - ověření, že jde skutečně o obrázek (kromě SVG)
    if (!in_array($mimeType, ['image/svg+xml', 'image/svg', 'text/xml', 'application/svg+xml', 'application/xml'])) {
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

    // Cesty - OPRAVA: správná cesta
    $baseDir = dirname(__DIR__) . '/media/upload'; // Jdeme o 1 úroveň nahoru z admin/databaze.php
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

    // Přesun souboru
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new Exception("Nepodařilo se uložit soubor.");
    }

    // Nastavení správných práv
    chmod($dest, 0644);

    // Optimalizace obrázku (volitelné)
    try {
        blkt_optimalizuj_obrazek($dest, $mimeType);
    } catch (Exception $e) {
        // Pokud optimalizace selže, pokračujeme bez ní
        error_log("Image optimization failed: " . $e->getMessage());
    }

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
 * Optimalizace obrázku
 * @param string $path Cesta k obrázku
 * @param string $mimeType MIME typ
 */
function blkt_optimalizuj_obrazek(string $path, string $mimeType): void {
    // SVG nepotřebuje optimalizaci
    if (in_array($mimeType, ['image/svg+xml', 'image/svg', 'text/xml', 'application/svg+xml', 'application/xml'])) {
        return;
    }

    // Načtení obrázku
    switch ($mimeType) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($path);
            break;
        case 'image/png':
            $image = imagecreatefrompng($path);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($path);
            break;
        case 'image/webp':
            $image = imagecreatefromwebp($path);
            break;
        default:
            return;
    }

    if (!$image) {
        return;
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

        // Zachování průhlednosti pro PNG a WebP
        if ($mimeType === 'image/png' || $mimeType === 'image/webp') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);
        $image = $newImage;
    }

    // Uložení optimalizovaného obrázku
    switch ($mimeType) {
        case 'image/jpeg':
            imagejpeg($image, $path, 85); // 85% kvalita
            break;
        case 'image/png':
            imagepng($image, $path, 6); // Komprese 6
            break;
        case 'image/gif':
            imagegif($image, $path);
            break;
        case 'image/webp':
            imagewebp($image, $path, 85); // 85% kvalita
            break;
    }

    imagedestroy($image);
}

/**
 * Aktualizuje metadata obrázku a volitelně nahradí soubor
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

        // Použijeme stejnou logiku jako u vložení
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

        $ext = $allowedMimes[$mimeType];

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

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new Exception('Nepodařilo se uložit nový soubor.');
        }

        chmod($dest, 0644);

        // Optimalizace
        try {
            blkt_optimalizuj_obrazek($dest, $mimeType);
        } catch (Exception $e) {
            error_log("Image optimization failed: " . $e->getMessage());
        }

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