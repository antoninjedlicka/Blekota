<?php
// admin/action/edit_seo.php

require_once __DIR__ . '/../databaze.php';

$response = ['status' => 'ok'];

try {
    foreach ($_POST as $kod => $hodnota) {
        // Zjistíme, jestli záznam existuje
        $stmt = blkt_db_connect()->prepare("SELECT COUNT(*) FROM blkt_konfigurace WHERE blkt_kod = :kod");
        $stmt->execute([':kod' => $kod]);

        if ($stmt->fetchColumn() > 0) {
            blkt_uprav_konfiguraci_podle_kodu($kod, $hodnota);
        } else {
            blkt_insert_konfigurace([
                'nazev'   => $kod,
                'kod'     => $kod,
                'hodnota' => $hodnota
            ]);
        }
    }
} catch (Exception $e) {
    $response = ['status' => 'error', 'error' => $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($response);
