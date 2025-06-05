<?php
// includes/nazev_posledniho_prispevku.php
require_once __DIR__ . '/../databaze.php';

$pdo = blkt_db_connect();

// Načtení posledního příspěvku podle nejvyššího blkt_id
$sql = "
    SELECT p.*, d.blkt_slug 
    FROM blkt_prispevky p
    LEFT JOIN blkt_obsah_detaily d 
        ON d.blkt_parent_id = p.blkt_id 
       AND d.blkt_type = 'post'
    ORDER BY p.blkt_id DESC
    LIMIT 1
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$prispevek = $stmt->fetch();

if ($prispevek && !empty($prispevek['blkt_slug'])):
    $obsah = strip_tags(html_entity_decode($prispevek['blkt_obsah'], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $vynatek = mb_substr($obsah, 0, 200) . '...';
    $url = '/blog/' . htmlspecialchars($prispevek['blkt_slug']);
    ?>
    <h5><?php echo htmlspecialchars($prispevek['blkt_nazev']); ?></h5>
    <p><?php echo htmlspecialchars($vynatek); ?></p>
    <a href="<?php echo $url; ?>">Celý příspěvek &rarr;</a>
<?php else: ?>
    <p>Žádný příspěvek nebyl nalezen.</p>
<?php endif; ?>
