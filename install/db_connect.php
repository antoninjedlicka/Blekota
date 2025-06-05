<?php
// install/db_connect.php
// Připojení k databázi a vytvoření connect.php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Přístup zamítnut.']);
    exit;
}

$host = trim($_POST['host'] ?? '');
$dbname = trim($_POST['dbname'] ?? '');
$user = trim($_POST['user'] ?? '');
$pass = $_POST['pass'] ?? '';

if (!$host || !$dbname || !$user) {
    echo json_encode(['error' => 'Vyplňte prosím všechna pole.']);
    exit;
}

// Ověření připojení
try {
    new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    $code = $e->errorInfo[1] ?? 0;
    switch ($code) {
        case 1045: $error = 'Chyba přihlášení: zkontrolujte uživatelské jméno a heslo.'; break;
        case 1049: $error = 'Databáze nenalezena: zkontrolujte název databáze.'; break;
        case 2002: $error = 'Nelze se připojit k serveru: zkontrolujte host.'; break;
        default:   $error = 'Chyba připojení: ' . $e->getMessage();
    }
    echo json_encode(['error' => $error]);
    exit;
}

// Uložení connect.php
$cfgFile = __DIR__ . '/../connect.php';
$cfg  = "<?php\n";
$cfg .= "// connect.php – generováno instalátorem\n";
$cfg .= "if(basename(__FILE__)==basename(\$_SERVER['SCRIPT_FILENAME'])){http_response_code(403);exit;}\n";
$cfg .= "define('BLKT_DB_HOST','" . addslashes($host) . "');\n";
$cfg .= "define('BLKT_DB_NAME','" . addslashes($dbname) . "');\n";
$cfg .= "define('BLKT_DB_USER','" . addslashes($user) . "');\n";
$cfg .= "define('BLKT_DB_PASS','" . addslashes($pass) . "');\n?>\n";

if (!file_put_contents($cfgFile, $cfg)) {
    echo json_encode(['error' => 'Nepodařilo se vytvořit connect.php.']);
    exit;
}

// Úspěch
echo json_encode(['status' => 'ok']);
exit;
?>
