<?php
// admin/action/edit_skupina.php
header('Content-Type: application/json');
require_once __DIR__ . '/../databaze.php';

try {
    // Načíst a validovat vstupy
    $id = isset($_POST['blkt_idskupiny']) ? (int)$_POST['blkt_idskupiny'] : 0;
    $nazev = trim($_POST['blkt_nazev'] ?? '');
    $popis = trim($_POST['blkt_popis'] ?? '');
    $role = $_POST['role'] ?? [];

    if (!$id) {
        throw new Exception('Neznámé ID skupiny.');
    }

    if (!$nazev) {
        throw new Exception('Název skupiny je povinný.');
    }

    // Převést pole rolí na čárkami oddělený řetězec
    $role_string = !empty($role) ? implode(',', array_map('intval', $role)) : '';

    // Sestavit data pro CRUD
    $data = [
        'nazev' => $nazev,
        'popis' => $popis,
        'role'  => $role_string,
    ];

    $ok = blkt_update_skupina($id, $data);

    if (!$ok) {
        throw new Exception('Nepodařilo se aktualizovat skupinu.');
    }

    echo json_encode([
        'status' => 'ok',
        'message' => "Skupina byla úspěšně upravena."
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