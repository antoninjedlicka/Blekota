<?php
require_once __DIR__ . '/databaze.php';

$db = blkt_db_connect();

// Získání URL blogu z konfigurace
$stmt = $db->prepare("SELECT blkt_hodnota FROM blkt_konfigurace WHERE blkt_kod = 'BLOG' LIMIT 1");
$stmt->execute();
$blogUrl = $stmt->fetchColumn();

// Načtení příspěvků i se slugy
$query = "
    SELECT p.blkt_nazev, p.blkt_obsah, d.blkt_slug
    FROM blkt_prispevky p
    JOIN blkt_obsah_detaily d ON d.blkt_parent_id = p.blkt_id
    WHERE d.blkt_type = 'post'
    ORDER BY p.blkt_id DESC
";
$prispevky = $db->query($query)->fetchAll();

$pageTitle = 'Blog';
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/blog.css">
    <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:ital,wght@0,100;0,300;0,400;0,500;0,600;0,700;1,100;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
</head>
<body>
<header class="blkt-hlavicka">
    <?php include __DIR__ . '/includes/header.php'; ?>
</header>

<main class="blkt-obsah-stranky">
    <section class="blkt-blog-masonry">
        <?php foreach ($prispevky as $index => $p): ?>
            <?php
            // 1) Najít první <img> v obsahu
            preg_match('/<img[^>]+src="([^"]+)"/i', $p['blkt_obsah'], $imgMatch);
            $imgSrc = $imgMatch[1] ?? null; // fallback

            // 2) Vytvořit výňatek (text bez HTML, oříznutý)
            $plainText = strip_tags(html_entity_decode($p['blkt_obsah'], ENT_QUOTES | ENT_HTML5, 'UTF-8'));

            // První příspěvek má delší výňatek
            if ($index === 0) {
                $excerpt = mb_substr($plainText, 0, 500) . '…';
            } else {
                $excerpt = mb_substr($plainText, 0, 200) . '…';
            }

            // 3) URL příspěvku
            $postUrl = rtrim($blogUrl, '/') . '/' . urlencode($p['blkt_slug']);

            // 4) Přiřazení třídy pro první příspěvek
            $itemClass = ($index === 0) ? 'blkt-masonry-item blkt-masonry-item-hlavni' : 'blkt-masonry-item';
            ?>
            <div class="<?php echo $itemClass; ?>">
                <?php if ($imgSrc): ?>
                    <div class="blkt-masonry-obrazek">
                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="">
                    </div>
                <?php endif; ?>
                <div class="blkt-masonry-obsah">
                    <h2 class="blkt-masonry-nadpis"><?php echo htmlspecialchars($p['blkt_nazev']); ?></h2>
                    <p class="blkt-masonry-vynatek"><?php echo htmlspecialchars($excerpt); ?></p>
                    <a href="<?php echo $postUrl; ?>" class="blkt-masonry-odkaz">Zobrazit příspěvek</a>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
</main>

<footer class="blkt-paticka">
    <?php include __DIR__ . '/includes/footer.php'; ?>
</footer>
<?php include 'includes/loader.php'; ?>
</body>
</html>