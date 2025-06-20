<?php
// admin/action/add_skupina.php
header('Content-Type: application/json');
require_once __DIR__ . '/../databaze.php';

try {
    // Načíst a validovat vstupy
    $nazev = trim($_POST['blkt_nazev'] ?? '');
    $popis = trim($_POST['blkt_popis'] ?? '');
    $role = $_POST['role'] ?? [];

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

    $newId = blkt_insert_skupina($data);

    echo json_encode([
        'status' => 'ok',
        'message' => "Skupina byla úspěšně přidána.",
        'id' => $newId
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