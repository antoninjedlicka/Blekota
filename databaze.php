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

