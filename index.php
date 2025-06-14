<?php
// index.php – front-end router s kontrolou přihlášení

// Načtení centrální session konfigurace
require_once __DIR__ . '/includes/session.php';

// 1) Připojení k DB
require_once __DIR__ . '/databaze.php';
$pdo = blkt_db_connect();

// 2) Načtení auth funkcí
require_once __DIR__ . '/includes/auth.php';

// 3) Kontrola, zda je web nastaven jako nový
$stmt = $pdo->prepare("SELECT blkt_hodnota FROM blkt_konfigurace WHERE blkt_kod = 'NEW' LIMIT 1");
$stmt->execute();
$is_new = $stmt->fetchColumn() === 'true';

// 4) Pokud je web nový a uživatel není přihlášen, přesměruj na login
if ($is_new && !blkt_je_prihlasen()) {
    blkt_presmeruj_na_login();
}

// 5) Načtení front-end funkce
require_once __DIR__ . '/includes/frontend.php';

// 6) Získat čistou cestu
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Routování obsahu
ob_start();

if ($requestUri === '/' || $requestUri === '/index.php') {
    include __DIR__ . '/homepage.php';

} elseif ($requestUri === '/cv' || $requestUri === '/cv.php') {
    include __DIR__ . '/cv.php';

} elseif ($requestUri === '/sitemap.xml') {
    include 'sitemap.php';

} else {
    // konfigurace blogu
    $stmt = $pdo->prepare("
      SELECT blkt_hodnota
      FROM blkt_konfigurace
      WHERE blkt_kod = 'blog'
      LIMIT 1
    ");
    $stmt->execute();
    $blogUrl = $stmt->fetchColumn();

    if (!$blogUrl) {
        http_response_code(500);
        exit('Chyba: nenalezena konfigurace blogu.');
    }

    $basePath = rtrim(parse_url($blogUrl, PHP_URL_PATH), '/');

    if ($requestUri === $basePath || $requestUri === $basePath . '/') {
        include __DIR__ . '/blog.php';

    } elseif (strpos($requestUri, $basePath . '/') === 0) {
        $slug = trim(substr($requestUri, strlen($basePath) + 1), '/');
        $post = blkt_frontend_ziskej_prispevek($pdo, $slug);

        if ($post) {
            $pageTitle = $post['blkt_nazev'];
            include __DIR__ . '/includes/post.php';
        } else {
            http_response_code(404);
            include __DIR__ . '/404.php';
        }
    } else {
        http_response_code(404);
        include __DIR__ . '/404.php';
    }
}

// Získáme obsah
$pageContent = ob_get_clean();
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blekota</title>
    <link rel="stylesheet" href="/css/loader.css">
    <!-- Preconnect pro fonty -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Fonty -->
    <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:ital,wght@0,100;0,300;0,400;0,500;0,600;0,700;1,100;1,300;1,400;1,500;1,600;1,700&display=swap"
          rel="stylesheet">
</head>
<body>

<?= $pageContent; ?>

<script src="/js/loader.js"></script>

</body>
</html>