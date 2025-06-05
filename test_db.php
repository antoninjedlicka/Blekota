<?php
// test-db.php - dočasný testovací soubor
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/databaze.php';

echo "<h1>Test databáze</h1>";

try {
    $db = blkt_db_connect();
    echo "<p style='color:green'>✓ Připojení k databázi OK</p>";
    
    // Test 1: Kolik máme příspěvků?
    $count = $db->query("SELECT COUNT(*) FROM blkt_prispevky")->fetchColumn();
    echo "<p>Počet příspěvků v tabulce: <strong>$count</strong></p>";
    
    // Test 2: Kolik máme detailů?
    $count2 = $db->query("SELECT COUNT(*) FROM blkt_obsah_detaily WHERE blkt_type = 'post'")->fetchColumn();
    echo "<p>Počet detailů pro příspěvky: <strong>$count2</strong></p>";
    
    // Test 3: Výpis všech příspěvků
    echo "<h2>Seznam všech příspěvků:</h2>";
    $query = "
        SELECT p.blkt_id, p.blkt_nazev, d.blkt_slug
        FROM blkt_prispevky p
        LEFT JOIN blkt_obsah_detaily d ON d.blkt_parent_id = p.blkt_id AND d.blkt_type = 'post'
        ORDER BY p.blkt_id DESC
    ";
    
    $prispevky = $db->query($query)->fetchAll();
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Název</th><th>Slug</th></tr>";
    foreach ($prispevky as $p) {
        echo "<tr>";
        echo "<td>" . $p['blkt_id'] . "</td>";
        echo "<td>" . htmlspecialchars($p['blkt_nazev']) . "</td>";
        echo "<td>" . ($p['blkt_slug'] ?: '<span style="color:red">CHYBÍ!</span>') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test 4: Blog URL
    $blogUrl = $db->query("SELECT blkt_hodnota FROM blkt_konfigurace WHERE blkt_kod = 'BLOG'")->fetchColumn();
    echo "<p>Blog URL: <strong>" . htmlspecialchars($blogUrl) . "</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>Chyba: " . $e->getMessage() . "</p>";
}
?>