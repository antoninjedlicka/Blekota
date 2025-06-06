<?php
// admin/action/save_zivotopis.php
require_once __DIR__ . '/../databaze.php';

$response = ['status' => 'ok'];

try {
    $pdo = blkt_db_connect();
    $pdo->beginTransaction();

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
        if (!isset($_POST[$typ])) continue;

        // Smaž všechny existující položky tohoto typu
        $singular_typ = rtrim($typ, 'i'); // odstraň koncové 'i'
        if ($typ === 'zajmy') $singular_typ = 'zajem';
        if ($typ === 'profese') $singular_typ = 'profese';

        blkt_delete_zivotopis_typ($singular_typ);

        // Vlož nové položky
        foreach ($_POST[$typ] as $polozka) {
            if (empty($polozka['typ'])) continue;

            // Přeskoč prázdné položky
            $is_empty = true;
            foreach ($polozka as $key => $value) {
                if ($key !== 'id' && $key !== 'typ' && !empty($value)) {
                    $is_empty = false;
                    break;
                }
            }
            if ($is_empty) continue;

            // Vlož novou položku
            blkt_insert_zivotopis_polozka([
                'typ' => $polozka['typ'],
                'poradi' => $polozka['poradi'] ?? 0,
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
            ]);
        }
    }

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    $response = ['status' => 'error', 'error' => $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($response);