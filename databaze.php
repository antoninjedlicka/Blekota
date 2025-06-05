<?php
// databaze.php - OPTIMALIZOVANÁ VERZE
// Přímý přístup není povolen.
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(403);
    exit('Přímý přístup není povolen.');
}

// Načtení přístupových údajů k databázi
if (!file_exists(__DIR__ . '/connect.php')) {
    die('Konfigurační soubor connect.php nenalezen. Spusť instalátor.');
}
require_once __DIR__ . '/connect.php';

// Singleton pattern pro PDO instanci
class DatabaseConnection {
    private static $instance = null;
    
    private function __construct() {}
    
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . BLKT_DB_HOST . ';dbname=' . BLKT_DB_NAME . ';charset=utf8mb4';
            try {
                self::$instance = new PDO(
                    $dsn,
                    BLKT_DB_USER,
                    BLKT_DB_PASS,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                        PDO::ATTR_PERSISTENT         => true, // Persistent connection
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                    ]
                );
            } catch (PDOException $e) {
                error_log('Database connection error: ' . $e->getMessage());
                die('Chyba připojení k databázi.');
            }
        }
        return self::$instance;
    }
}

/**
 * Vrátí PDO instanci pro práci s databází.
 * @return PDO
 */
function blkt_db_connect(): PDO {
    return DatabaseConnection::getInstance();
}

/**
 * Bezpečné načtení SEO hodnoty s cachováním
 * @param string $kod
 * @return string
 */
function blkt_nacti_seo(string $kod): string {
    static $cache = [];
    
    if (isset($cache[$kod])) {
        return $cache[$kod];
    }
    
    try {
        $stmt = blkt_db_connect()->prepare("SELECT blkt_hodnota FROM blkt_konfigurace WHERE blkt_kod = :kod LIMIT 1");
        $stmt->execute([':kod' => $kod]);
        $value = $stmt->fetchColumn() ?: '';
        $cache[$kod] = $value;
        return $value;
    } catch (PDOException $e) {
        error_log('SEO load error: ' . $e->getMessage());
        return '';
    }
}

/**
 * Bezpečné vložení uživatele s validací
 * @param array $data
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
        (blkt_jmeno, blkt_prijmeni, blkt_mail, blkt_heslo, blkt_stav, blkt_admin)
        VALUES
        (:jmeno, :prijmeni, :mail, :heslo, :stav, :admin)
    ");
    
    $stmt->execute([
        ':jmeno'    => $data['jmeno'],
        ':prijmeni' => $data['prijmeni'],
        ':mail'     => $data['mail'],
        ':heslo'    => $hashedPassword,
        ':stav'     => isset($data['stav']) ? (int)$data['stav'] : 1,
        ':admin'    => isset($data['admin']) ? (int)$data['admin'] : 0,
    ]);
    
    return (int)$pdo->lastInsertId();
}

// Přidání indexů pro lepší výkon (spustit jednou při instalaci)
function blkt_create_indexes(): void {
    $pdo = blkt_db_connect();
    
    // Index pro rychlé vyhledávání uživatelů
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_mail ON blkt_uzivatele(blkt_mail)");
    
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