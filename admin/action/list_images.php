<?php
// admin/action/list_images.php
// Vrací JSON seznam všech obrázků pro TinyMCE galerii

// načteme připojení k DB
require_once __DIR__ . '/../databaze.php';
$pdo = blkt_db_connect();

// připravíme dotaz na obrázky
$stmt = $pdo->prepare("
    SELECT blkt_id, blkt_filename, blkt_title, blkt_alt
    FROM blkt_images
    ORDER BY blkt_created_at DESC
");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// zkonstruujeme výstupní pole
$out = [];
foreach ($rows as $img) {
    $out[] = [
        'id'    => (int)$img['blkt_id'],
        // ../ aby cesta relativně k /admin/index.php vedla do /media/upload
        'url'   => '../media/upload/' . $img['blkt_filename'],
        'title' => $img['blkt_title'],
        'alt'   => $img['blkt_alt'],
    ];
}

// pošleme JSON hlavičku a data
header('Content-Type: application/json; charset=utf-8');
echo json_encode($out);
