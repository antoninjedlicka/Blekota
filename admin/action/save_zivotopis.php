<?php
// admin/action/save_zivotopis.php
require_once __DIR__ . '/../databaze.php';

$response = ['status' => 'ok'];

try {
    $pdo = blkt_db_connect();
    $pdo->beginTransaction();

    // Debug - vypsat přijatá data
    error_log('Save Zivotopis - POST data: ' . print_r($_POST, true));

    // 1. Uložení základních údajů
    $zakladni_udaje = ['cv_jmeno', 'cv_lokace', 'cv_telefon', 'cv_email', 'cv_foto'];
    foreach ($zakladni_udaje as $kod) {
        if (isset($_POST[$kod])) {
            $hodnota = $_POST[$kod];

            // Zkontroluj, zda záznam existuje
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM blkt_konfigurace WHERE blkt_kod = :kod");
            $stmt->execute([':kod' => $kod]);

            if ($stmt->fetchColumn() > 0) {
                // Aktualizuj
                blkt_uprav_konfiguraci_podle_kodu($kod, $hodnota);
            } else {
                // Vlož nový
                blkt_insert_konfigurace([
                    'nazev' => $kod,
                    'kod' => $kod,
                    'hodnota' => $hodnota
                ]);
            }
        }
    }

    // 2. Zpracování jednotlivých typů položek
    $typy = ['profese', 'dovednosti', 'vlastnosti', 'jazyky', 'vzdelani', 'zajmy'];

    foreach ($typy as $typ) {
        if (!isset($_POST[$typ])) {
            error_log("Save Zivotopis - Typ '$typ' není v POST datech");
            continue;
        }

        // Určení správného singulárního typu
        $singular_typ = $typ;
        switch($typ) {
            case 'dovednosti':
                $singular_typ = 'dovednost';
                break;
            case 'vlastnosti':
                $singular_typ = 'vlastnost';
                break;
            case 'jazyky':
                $singular_typ = 'jazyk';
                break;
            case 'vzdelani':
                $singular_typ = 'vzdelani';
                break;
            case 'zajmy':
                $singular_typ = 'zajem';
                break;
            case 'profese':
                $singular_typ = 'profese';
                break;
        }

        error_log("Save Zivotopis - Zpracovávám typ '$typ' (singular: '$singular_typ')");

        // Smaž všechny existující položky tohoto typu
        blkt_delete_zivotopis_typ($singular_typ);

        // Vlož nové položky
        $pocet_vlozenych = 0;
        foreach ($_POST[$typ] as $index => $polozka) {
            error_log("Save Zivotopis - Položka $index: " . print_r($polozka, true));

            if (empty($polozka['typ'])) {
                error_log("Save Zivotopis - Položka $index nemá typ, přeskakuji");
                continue;
            }

            // Přeskoč prázdné položky (ale u zájmů nech i prázdné)
            if ($typ !== 'zajmy') {
                $is_empty = true;
                foreach ($polozka as $key => $value) {
                    if ($key !== 'id' && $key !== 'typ' && $key !== 'poradi' && !empty(trim($value))) {
                        $is_empty = false;
                        break;
                    }
                }
                if ($is_empty) {
                    error_log("Save Zivotopis - Položka $index je prázdná, přeskakuji");
                    continue;
                }
            }

            // Vlož novou položku
            $insert_data = [
                'typ' => $polozka['typ'],
                'poradi' => isset($polozka['poradi']) ? (int)$polozka['poradi'] : 0,
                'nazev' => $polozka['nazev'] ?? null,
                'podnazev' => $polozka['podnazev'] ?? null,
                'datum_od' => $polozka['datum_od'] ?? null,
                'datum_do' => $polozka['datum_do'] ?? null,
                'popis' => $polozka['popis'] ?? null,
                'obsah' => $polozka['obsah'] ?? null,
                'tagy' => $polozka['tagy'] ?? null,
                'ikona' => $polozka['ikona'] ?? null,
                'uroven' => $polozka['uroven'] ?? null,
                'stav' => 1
            ];

            error_log("Save Zivotopis - Vkládám data: " . print_r($insert_data, true));

            blkt_insert_zivotopis_polozka($insert_data);
            $pocet_vlozenych++;
        }

        error_log("Save Zivotopis - Vloženo $pocet_vlozenych položek typu '$typ'");
    }

    $pdo->commit();
    error_log("Save Zivotopis - Transakce úspěšně dokončena");

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Save Zivotopis - CHYBA: " . $e->getMessage());
    $response = ['status' => 'error', 'error' => $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($response);