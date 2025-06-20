<?php
// admin/action/delete_skupina.php
header('Content-Type: application/json');
require_once __DIR__ . '/../databaze.php';

try {
    $id = isset($_POST['blkt_idskupiny']) ? (int)$_POST['blkt_idskupiny'] : 0;

    if (!$id) {
        throw new Exception('Neznámé ID skupiny.');
    }

    // Zkontrolovat, kolik uživatelů je ve skupině
    $pocet = blkt_pocet_uzivatelu_ve_skupine($id);

    $ok = blkt_delete_skupina($id);

    if (!$ok) {
        throw new Exception('Nepodařilo se smazat skupinu.');
    }

    $message = "Skupina byla úspěšně smazána.";
    if ($pocet > 0) {
        $message .= " $pocet uživatelů bylo odebráno ze skupiny.";
    }

    echo json_encode([
        'status' => 'ok',
        'message' => $message
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'error' => $e->getMessage()
    ]);
    exit;
}