<?php
// login.php - JEDNODUCHÁ BEZPEČNÁ VERZE
// Nastavení cookie parametrů před session_start()
session_set_cookie_params([
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']), // pouze přes HTTPS
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

// OCHRANA LOGINU: Limity pokusů
if (!isset($_SESSION['blkt_login_pokusy'])) {
    $_SESSION['blkt_login_pokusy'] = 0;
    $_SESSION['blkt_login_zamknuto_do'] = 0;
}

// Pokud už je přihlášený, přesměruj
if (isset($_SESSION['blkt_prihlasen']) && $_SESSION['blkt_prihlasen'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['blkt_login_zamknuto_do'] > time()) {
        $error = 'Příliš mnoho neúspěšných pokusů. Zkuste to znovu za pár minut.';
    } else {
        require_once __DIR__.'/connect.php';
        require_once __DIR__ . '/databaze.php';

        $mail = trim($_POST['mail']);
        $heslo = $_POST['heslo'];

        if ($mail && $heslo) {
            try {
                $pdo = blkt_db_connect();
                $stmt = $pdo->prepare("SELECT * FROM blkt_uzivatele WHERE blkt_mail = :mail LIMIT 1");
                $stmt->execute([':mail' => $mail]);
                $uzivatel = $stmt->fetch();

                if ($uzivatel && password_verify($heslo, $uzivatel['blkt_heslo'])) {
                    // Úspěšné přihlášení
                    session_regenerate_id(true); // Nové session ID pro bezpečnost
                    
                    $_SESSION['blkt_prihlasen'] = true;
                    $_SESSION['blkt_uzivatel_jmeno'] = $uzivatel['blkt_jmeno'];
                    $_SESSION['blkt_uzivatel_prijmeni'] = $uzivatel['blkt_prijmeni'];
                    $_SESSION['blkt_uzivatel_mail'] = $uzivatel['blkt_mail'];
                    $_SESSION['blkt_uzivatel_admin'] = $uzivatel['blkt_admin'];

                    // Reset ochrany pokusů
                    $_SESSION['blkt_login_pokusy'] = 0;
                    $_SESSION['blkt_login_zamknuto_do'] = 0;

                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Neplatné přihlašovací údaje.';
                    $_SESSION['blkt_login_pokusy']++;

                    if ($_SESSION['blkt_login_pokusy'] >= 5) {
                        $_SESSION['blkt_login_zamknuto_do'] = time() + (5 * 60); // 5 minut blokace
                        $error = 'Příliš mnoho pokusů. Zkuste to za 5 minut.';
                    }
                }
            } catch (Exception $e) {
                $error = 'Chyba připojení: ' . htmlspecialchars($e->getMessage());
            }
        } else {
            $error = 'Vyplňte prosím všechny údaje.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Přihlášení</title>
  <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:ital,wght@0,100;0,300;0,400;0,500;0,600;0,700;1,100;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/login.css">
</head>
<body>

<div class="login-container">
  <h2>Přihlášení</h2>

  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post" autocomplete="off">
    <div class="blkt-formular-skupina">
      <input type="email" name="mail" id="mail" placeholder=" " required>
      <label for="mail">E-mail</label>
    </div>
    <div class="blkt-formular-skupina">
      <input type="password" name="heslo" id="heslo" placeholder=" " required>
      <label for="heslo">Heslo</label>
    </div>
    <button class="btn">Přihlásit se</button>
  </form>

</div>

</body>
</html>