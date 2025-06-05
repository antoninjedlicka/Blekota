<?php
// includes/404.php
// Šablona chybové stránky – stránka nenalezena

$pageTitle = 'Stránka nenalezena (404)';
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:ital,wght@0,100;0,300;0,400;0,500;0,600;0,700;1,100;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
</head>
<body>
<header class="blkt-hlavicka">
    <?php include __DIR__ . '/includes/header.php'; ?>
</header>
<main class="blkt-404-obsah">
    <section class="blkt-404">
        <div class="blkt-404-hlavicka">
            <img class="blkt-404-obrazek" src="/media/icons/404.svg" alt="404">
            <h1 class="blkt-404-nadpis">Není tu!</h1>
        </div>
        <div class="blkt-404-linka"></div>
        <div class="blkt-404-text-kontejner">
            <p class="blkt-404-text">
            Tak tady se někdo uťal. Budeme se tvářit, že autor webu něco smazal nebo přesunul na nové místo.
            </p>
            <p class="blkt-404-text" style="font-size: 21px;padding: 5px 0">
                <strong>Nevěšte však hlavu!</strong>
            </p>
            <p class="blkt-404-text">
            Stále máte osud ve svých rukou... takže.
            Pujdete na domovskou stránku, nebo si tu trochu pohledáme?
            </p>
            <div class="blkt-404-linka"></div>
            <div class="blkt-404-tlacitko-kontejner">
                <a href="/"  class="blkt-tlacitko-404 blkt-tlacitko">Zpět na úvodní stránku</a>
            </div>
        </div>
    </section>
</main>
<footer class="blkt-paticka">
    <?php include __DIR__ . '/includes/footer.php'; ?>
</footer>
</body>
</html>
