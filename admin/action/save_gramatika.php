<?php
// admin/action/save_gramatika.php
// Ukládání nastavení české gramatiky do konfigurace

require_once __DIR__ . '/../databaze.php';

header('Content-Type: application/json');

$response = ['status' => 'ok'];

try {
    $pdo = blkt_db_connect();

    // Seznam povolených klíčů pro bezpečnost
    $povolene_klice = [
        'gramatika_predlozky',
        'gramatika_spojky',
        'gramatika_zkratky',
        'gramatika_cislovky',
        'gramatika_uvozovky',
        'gramatika_pomlcky',
        'gramatika_tecky',
        'gramatika_jednotky'
    ];

    foreach ($_POST as $kod => $hodnota) {
        // Kontrola, zda je klíč povolený
        if (!in_array($kod, $povolene_klice)) {
            continue;
        }

        // Pro checkboxy - pokud nejsou zaškrtnuté, nastavíme 0
        if (in_array($kod, ['gramatika_cislovky', 'gramatika_uvozovky', 'gramatika_pomlcky', 'gramatika_tecky'])) {
            $hodnota = isset($_POST[$kod]) && $_POST[$kod] == '1' ? '1' : '0';
        } else {
            // Pro textové hodnoty - odstranění přebytečných mezer
            $hodnota = trim($hodnota);

            // Pro seznamy - normalizace (odstranění mezer kolem čárek)
            if (in_array($kod, ['gramatika_predlozky', 'gramatika_spojky', 'gramatika_zkratky', 'gramatika_jednotky'])) {
                $polozky = explode(',', $hodnota);
                $polozky = array_map('trim', $polozky);
                $polozky = array_filter($polozky); // Odstranění prázdných
                $hodnota = implode(',', $polozky);
            }
        }

        // Zjistíme, jestli záznam existuje
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM blkt_konfigurace WHERE blkt_kod = :kod");
        $stmt->execute([':kod' => $kod]);

        if ($stmt->fetchColumn() > 0) {
            // Aktualizujeme existující záznam
            blkt_uprav_konfiguraci_podle_kodu($kod, $hodnota);
        } else {
            // Vytvoříme nový záznam
            $nazvy = [
                'gramatika_predlozky' => 'Předložky',
                'gramatika_spojky' => 'Spojky',
                'gramatika_zkratky' => 'Zkratky s tečkou',
                'gramatika_cislovky' => 'Úprava číslovek',
                'gramatika_uvozovky' => 'České uvozovky',
                'gramatika_pomlcky' => 'Pomlčky',
                'gramatika_tecky' => 'Tři tečky',
                'gramatika_jednotky' => 'Seznam jednotek'
            ];

            blkt_insert_konfigurace([
                'nazev' => $nazvy[$kod] ?? $kod,
                'kod' => $kod,
                'hodnota' => $hodnota
            ]);
        }
    }

    // Pro checkboxy které nebyly odeslány (nezaškrtnuté) musíme explicitně nastavit 0
    $checkbox_klice = ['gramatika_cislovky', 'gramatika_uvozovky', 'gramatika_pomlcky', 'gramatika_tecky'];
    foreach ($checkbox_klice as $kod) {
        if (!isset($_POST[$kod])) {
            // Zjistíme, jestli záznam existuje
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM blkt_konfigurace WHERE blkt_kod = :kod");
            $stmt->execute([':kod' => $kod]);

            if ($stmt->fetchColumn() > 0) {
                blkt_uprav_konfiguraci_podle_kodu($kod, '0');
            } else {
                $nazvy = [
                    'gramatika_cislovky' => 'Úprava číslovek',
                    'gramatika_uvozovky' => 'České uvozovky',
                    'gramatika_pomlcky' => 'Pomlčky',
                    'gramatika_tecky' => 'Tři tečky'
                ];

                blkt_insert_konfigurace([
                    'nazev' => $nazvy[$kod],
                    'kod' => $kod,
                    'hodnota' => '0'
                ]);
            }
        }
    }

} catch (Exception $e) {
    $response = ['status' => 'error', 'error' => $e->getMessage()];
}

echo json_encode($response);
?>