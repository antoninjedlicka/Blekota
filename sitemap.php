<?php
header('Content-Type: application/xml; charset=utf-8');

// Připojení databáze
require_once 'databaze.php';

$baseUrl = 'https://blekota.online';

// Načti příspěvky
$prispevky = blkt_db_connect()->query("
    SELECT d.blkt_slug
    FROM blkt_prispevky p
    JOIN blkt_obsah_detaily d ON d.blkt_parent_id = p.blkt_id
    WHERE d.blkt_type = 'post'
")->fetchAll(PDO::FETCH_ASSOC);

// Výstup XML
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Hlavní stránka -->
    <url>
        <loc><?= $baseUrl ?>/</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Blog -->
    <url>
        <loc><?= $baseUrl ?>/blog/</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>

    <!-- O stránce -->
    <url>
        <loc><?= $baseUrl ?>/about</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>

    <!-- Administrace -->
    <url>
        <loc><?= $baseUrl ?>/admin</loc>
        <changefreq>monthly</changefreq>
        <priority>0.2</priority>
    </url>

    <!-- Příspěvky pod /blog/ -->
    <?php foreach ($prispevky as $p): ?>
        <url>
            <loc><?= $baseUrl ?>/blog/<?= htmlspecialchars($p['blkt_slug']) ?></loc>
            <changefreq>monthly</changefreq>
            <priority>0.5</priority>
        </url>
    <?php endforeach; ?>
</urlset>
