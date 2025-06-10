<?php
// admin/action/save_nastaveni.php
// Ukládání nastavení do databáze

require_once __DIR__ . '/../databaze.php';

header('Content-Type: application/json');

$response = ['status' => 'ok'];

try {
    $pdo = blkt_db_connect();

    // Projdeme všechny odeslané hodnoty
    foreach ($_POST as $kod => $hodnota) {
        // Kontrola, zda je to platný kód nastavení
        if (in_array($kod, ['WWW', 'BLOG', 'THEME'])) {
            // Zkontrolujeme, jestli záznam existuje
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM blkt_konfigurace WHERE blkt_kod = :kod");
            $stmt->execute([':kod' => $kod]);

            if ($stmt->fetchColumn() > 0) {
                // Aktualizujeme existující záznam
                blkt_uprav_konfiguraci_podle_kodu($kod, $hodnota);
            } else {
                // Vytvoříme nový záznam
                $nazvy = [
                    'WWW' => 'Webová adresa webu',
                    'BLOG' => 'Webová adresa blogu',
                    'THEME' => 'Téma webu'
                ];

                blkt_insert_konfigurace([
                    'nazev' => $nazvy[$kod],
                    'kod' => $kod,
                    'hodnota' => $hodnota
                ]);
            }
        }
    }
} catch (Exception $e) {
    $response = ['status' => 'error', 'error' => $e->getMessage()];
}

echo json_encode($response);