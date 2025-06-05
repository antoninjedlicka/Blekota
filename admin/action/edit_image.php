<?php
// admin/action/edit_image.php
header('Content-Type: application/json');
require_once __DIR__ . '/../databaze.php';

try {
    if (isset($_POST['action']) && $_POST['action'] === 'get') {
        if (empty($_POST['blkt_id'])) {
            throw new Exception('Chybí ID obrázku.');
        }
        $pdo = blkt_db_connect();
        $stmt = $pdo->prepare("
            SELECT blkt_title, blkt_alt, blkt_description, blkt_original_name
            FROM blkt_images
            WHERE blkt_id = :id
        ");
        $stmt->execute([':id' => (int)$_POST['blkt_id']]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            throw new Exception('Obrázek nenalezen.');
        }
        echo json_encode(['status' => 'ok', 'data' => $data]);
        exit;
    } else {
        // Uložení úprav
        if (empty($_POST['blkt_id'])) {
            throw new Exception('Chybí ID obrázku.');
        }
        $id = (int)$_POST['blkt_id'];
        $newFile = (isset($_FILES['blkt_file']) && $_FILES['blkt_file']['error'] === UPLOAD_ERR_OK)
                   ? $_FILES['blkt_file']
                   : null;
        blkt_uprav_obrazek(
            $id,
            $_POST['blkt_title'] ?? '',
            $_POST['blkt_alt'] ?? '',
            $_POST['blkt_description'] ?? '',
            $newFile
        );
        echo json_encode(['status' => 'ok']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
    exit;
}
