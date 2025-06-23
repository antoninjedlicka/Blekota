<?php
// admin/content/gramatika.php
// Hlavní soubor pro sekci Gramatika - správa českých typografických pravidel

require_once __DIR__ . '/../databaze.php';
$pdo = blkt_db_connect();

// Definice všech gramatických nastavení, které budeme ukládat
$gramatika_nastaveni = [
    'gramatika_predlozky' => [
        'nazev' => 'Jednopísmenné předložky',
        'popis' => 'Seznam jednopísmenných předložek, za kterými se vloží nezalomitelná mezera',
        'vychozi' => 'k,s,v,z,o,u,a,i',
        'typ' => 'seznam'
    ],
    'gramatika_spojky' => [
        'nazev' => 'Jednopísmenné spojky',
        'popis' => 'Seznam jednopísmenných spojek, za kterými se vloží nezalomitelná mezera',
        'vychozi' => 'a,i',
        'typ' => 'seznam'
    ],
    'gramatika_zkratky' => [
        'nazev' => 'Zkratky s tečkou',
        'popis' => 'Seznam zkratek s tečkou, za kterými se vloží nezalomitelná mezera (např. tj., tzv.)',
        'vychozi' => 'tj.,tzv.,např.,mj.,apod.,atd.,resp.,popř.,event.,př.,cca',
        'typ' => 'seznam'
    ],
    'gramatika_cislovky' => [
        'nazev' => 'Číslovky',
        'popis' => 'Upravit mezery mezi čísly a jednotkami (např. 10 kg, 5 m)',
        'vychozi' => '1',
        'typ' => 'prepinac'
    ],
    'gramatika_uvozovky' => [
        'nazev' => 'České uvozovky',
        'popis' => 'Nahradit rovné uvozovky českými „uvozovkami"',
        'vychozi' => '1',
        'typ' => 'prepinac'
    ],
    'gramatika_pomlcky' => [
        'nazev' => 'Pomlčky',
        'popis' => 'Nahradit spojovník mezi čísly pomlčkou (např. 10-20 → 10–20)',
        'vychozi' => '1',
        'typ' => 'prepinac'
    ],
    'gramatika_tecky' => [
        'nazev' => 'Tři tečky',
        'popis' => 'Nahradit tři tečky speciálním znakem … (elipsa)',
        'vychozi' => '1',
        'typ' => 'prepinac'
    ],
    'gramatika_jednotky' => [
        'nazev' => 'Seznam jednotek',
        'popis' => 'Seznam jednotek, před kterými bude nezalomitelná mezera',
        'vychozi' => 'kg,g,mg,t,km,m,cm,mm,l,ml,h,min,s,°C,°F,K,kč,Kč,eur,EUR,$,%',
        'typ' => 'seznam'
    ]
];

// Načteme aktuální hodnoty z databáze
$gramatika_data = [];
foreach ($gramatika_nastaveni as $kod => $config) {
    $stmt = $pdo->prepare("SELECT blkt_hodnota FROM blkt_konfigurace WHERE blkt_kod = :kod");
    $stmt->execute([':kod' => $kod]);
    $hodnota = $stmt->fetchColumn();

    // Pokud hodnota neexistuje, použijeme výchozí
    if ($hodnota === false) {
        $gramatika_data[$kod] = $config['vychozi'];
    } else {
        $gramatika_data[$kod] = $hodnota;
    }
}
?>

<nav class="blkt-tabs">
    <button class="active" data-tab="prehled">Přehled</button>
</nav>

<div id="tab-prehled" class="tab-content active">
    <?php include __DIR__ . '/gramatika/prehled.php'; ?>
</div>

<div class="blkt-sticky-save">
    <button type="submit" form="blkt-form-gramatika" class="btn btn-save">Uložit všechny změny</button>
</div>

<script defer src="js/gramatika.js"></script>