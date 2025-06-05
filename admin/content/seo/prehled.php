<?php
// admin/content/seo/prehled.php

$seo_polozky = [
    'seo_title' => 'Název webu',
    'seo_description' => 'Popis webu',
    'seo_keywords' => 'Klíčová slova',
    'seo_author' => 'Meta Author',
    'seo_og_title' => 'OG Title',
    'seo_og_description' => 'OG Description',
    'seo_og_image' => 'OG Image (URL)',
    'seo_og_type' => 'OG Type',
    'seo_ld_name' => 'Structured Data: Název webu',
    'seo_ld_author' => 'Structured Data: Autor',
    'seo_ld_type' => 'Structured Data: Typ webu',
    'seo_ld_author_type' => 'Structured Data: Typ autora'
];

// Načtení hodnot z DB
$seo_data = [];
foreach ($seo_polozky as $kod => $popis) {
    $stmt = blkt_db_connect()->prepare("SELECT blkt_hodnota FROM blkt_konfigurace WHERE blkt_kod = :kod");
    $stmt->execute([':kod' => $kod]);
    $seo_data[$kod] = $stmt->fetchColumn() ?: '';
}

// Načteme hodnotu blkt_kod = www
$stmt = blkt_db_connect()->prepare("SELECT blkt_hodnota FROM blkt_konfigurace WHERE blkt_kod = 'www'");
$stmt->execute();
$seo_www = $stmt->fetchColumn() ?: '';

// Pomocná funkce pro fallback
function fallback($key, $fallbackKey, $seo_data) {
    return !empty($seo_data[$key]) ? $seo_data[$key] : ($seo_data[$fallbackKey] ?? '');
}
?>

<form id="blkt-form-seo" action="action/edit_seo.php" method="post" class="nastaveni-form">

    <!-- 1. META ZÁKLAD -->
    <div class="blkt-admin-box">
        <h2>Základní údaje</h2>
        <?php
    $zakladni = ['seo_title', 'seo_description', 'seo_keywords', 'seo_author'];
    foreach ($zakladni as $kod): ?>
        <div class="blkt-formular-skupina">
            <?php $is_textarea = in_array($kod, ['seo_description']); ?>
            <?php if ($is_textarea): ?>
                <textarea name="<?php echo $kod; ?>" placeholder=" " rows="3"><?php echo htmlspecialchars($seo_data[$kod]); ?></textarea>
            <?php else: ?>
                <input type="text" name="<?php echo $kod; ?>" value="<?php echo htmlspecialchars($seo_data[$kod]); ?>" placeholder=" ">
            <?php endif; ?>
            <label for="<?php echo $kod; ?>"><?php echo $seo_polozky[$kod]; ?></label>
        </div>
    <?php endforeach; ?>
    </div>

    <!-- 2. OPENGRAPH -->
    <div class="blkt-admin-box">
    <h2>OpenGraph</h2>
    <?php
    $opengraph = ['seo_og_title', 'seo_og_description', 'seo_og_image', 'seo_og_type'];
    foreach ($opengraph as $kod): ?>
        <div class="blkt-formular-skupina">
            <?php
            $value = fallback($kod, str_replace('seo_og_', 'seo_', $kod), $seo_data);
            ?>
            <?php if ($kod === 'seo_og_description'): ?>
                <textarea name="<?php echo $kod; ?>" placeholder=" " rows="3"><?php echo htmlspecialchars($value); ?></textarea>
            <?php elseif ($kod === 'seo_og_type'): ?>
                <select name="<?php echo $kod; ?>">
                    <?php
                    $typy = ['website', 'article', 'profile', 'book', 'video.other', 'music.song'];
                    foreach ($typy as $typ) {
                        $selected = ($value === $typ) ? 'selected' : '';
                        echo "<option value=\"$typ\" $selected>$typ</option>";
                    }
                    ?>
                </select>
            <?php else: ?>
                <input type="text" name="<?php echo $kod; ?>" value="<?php echo htmlspecialchars($value); ?>" placeholder=" ">
            <?php endif; ?>
            <label for="<?php echo $kod; ?>"><?php echo $seo_polozky[$kod]; ?></label>
        </div>
    <?php endforeach; ?>
    </div>

    <!-- 3. JSON-LD -->
    <div class="blkt-admin-box">
    <h2>Struktura JSON</h2>
    <?php
    $structured = ['seo_ld_name', 'seo_ld_author', 'seo_ld_type', 'seo_ld_author_type'];
    foreach ($structured as $kod): ?>
        <div class="blkt-formular-skupina">
            <?php
            $value = fallback($kod, str_replace('seo_ld_', 'seo_', $kod), $seo_data);
            $is_select = in_array($kod, ['seo_ld_type', 'seo_ld_author_type']);
            ?>
            <?php if ($is_select): ?>
                <select name="<?php echo $kod; ?>">
                    <?php
                    $options = $kod === 'seo_ld_type'
                        ? ['WebSite', 'Blog', 'NewsMedia', 'Portfolio', 'Organization']
                        : ['Person', 'Organization'];
                    foreach ($options as $opt) {
                        $selected = ($value === $opt) ? 'selected' : '';
                        echo "<option value=\"$opt\" $selected>$opt</option>";
                    }
                    ?>
                </select>
            <?php else: ?>
                <input type="text" name="<?php echo $kod; ?>" value="<?php echo htmlspecialchars($value); ?>" placeholder=" ">
            <?php endif; ?>
            <label for="<?php echo $kod; ?>"><?php echo $seo_polozky[$kod]; ?></label>
        </div>
    <?php endforeach; ?>

    <!-- GENEROVANÝ JSON-LD -->
    <div class="blkt-formular-skupina">
        <textarea rows="10" readonly style="font-family: monospace;"><?php
            $jsonld = [
                "@context" => "https://schema.org",
                "@type" => fallback('seo_ld_type', '', $seo_data) ?: 'WebSite',
                "name" => fallback('seo_ld_name', '', $seo_data),
                "url" => $seo_www,
                "author" => [
                    "@type" => fallback('seo_ld_author_type', '', $seo_data) ?: 'Person',
                    "name" => fallback('seo_ld_author', '', $seo_data)
                ],
                "description" => fallback('seo_description', '', $seo_data)
            ];
            echo htmlspecialchars(json_encode($jsonld, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            ?></textarea>
    </div>
    <input type="hidden" name="seo_ld_json" id="seo_ld_json_hidden" value="">
    </div>
</form>
