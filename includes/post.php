<?php
// includes/post.php
// Celá HTML stránka pro zobrazení jednoho příspěvku

// Předpokládá se, že je definováno:
//   $pageTitle (string) – název stránky
//   $post (array) – s klíči 'blkt_nazev', 'blkt_obsah'
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Blog'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:ital,wght@0,100;0,300;0,400;0,500;0,600;0,700;1,100;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<header class="blkt-hlavicka">
    <?php include __DIR__ . '/header.php'; ?>
</header>

<main class="blkt-obsah-stranky">
    <article class="blkt-prispevek">
        <!-- Název příspěvku -->
        <h1 class="blkt-prispevek-nadpis-h1">
            <?php /** @var TYPE_NAME $post */
            echo htmlspecialchars($post['blkt_nazev'],ENT_QUOTES); ?>
        </h1>

        <!-- Obsah příspěvku -->
        <div class="blkt-prispevek-obsah">
            <?php echo $post['blkt_obsah']; ?>
        </div>
    </article>
</main>

<footer class="blkt-paticka">
    <?php include __DIR__ . '/footer.php'; ?>
</footer>
<?php include 'loader.php'; ?>

</body>
</html>


