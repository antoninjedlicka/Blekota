<?php
// includes/frontend.php
// Funkce pro front-end: načtení příspěvku podle slugu.

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(403);
    exit('Přímý přístup není povolen.');
}

/**
 * Vrátí příspěvek (název + blkt-obsah-stranky) podle slugu.
 *
 * @param PDO    $pdo
 * @param string $slug  část URL bez “/blog/”
 * @return array|null   asociativní pole ['blkt_id','blkt_nazev','blkt_obsah'] nebo null
 */
function blkt_frontend_ziskej_prispevek(PDO $pdo, string $slug): ?array {
    $sql = "
      SELECT 
        p.blkt_id,
        p.blkt_nazev,
        p.blkt_obsah
      FROM blkt_prispevky AS p
      INNER JOIN blkt_obsah_detaily AS d
        ON d.blkt_parent_id = p.blkt_id
      WHERE d.blkt_type = 'post'
        AND d.blkt_slug = :slug
      LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':slug' => $slug]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    return $post ?: null;
}
