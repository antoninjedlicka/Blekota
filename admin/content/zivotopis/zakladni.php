<?php
// admin/content/zivotopis/zakladni.php
?>
<form id="blkt-form-zivotopis" method="post" action="action/save_zivotopis.php">
    <input type="hidden" name="section" value="zakladni">

    <div class="blkt-admin-box">
        <h2>Základní údaje</h2>

        <div class="blkt-formular-skupina">
            <input type="text" name="cv_jmeno" value="<?= htmlspecialchars($zakladni_udaje['cv_jmeno']) ?>" placeholder=" " required>
            <label>Jméno a příjmení</label>
        </div>

        <div class="blkt-formular-skupina">
            <input type="text" name="cv_lokace" value="<?= htmlspecialchars($zakladni_udaje['cv_lokace']) ?>" placeholder=" ">
            <label>Město/Lokace</label>
        </div>

        <div class="blkt-formular-skupina">
            <input type="tel" name="cv_telefon" value="<?= htmlspecialchars($zakladni_udaje['cv_telefon']) ?>" placeholder=" ">
            <label>Telefon</label>
        </div>

        <div class="blkt-formular-skupina">
            <input type="email" name="cv_email" value="<?= htmlspecialchars($zakladni_udaje['cv_email']) ?>" placeholder=" ">
            <label>E-mail</label>
        </div>
    </div>

    <div class="blkt-admin-box">
        <h2>Profilová fotografie</h2>
        <p>Vyberte fotografii z galerie médií.</p>

        <div id="blkt-cv-foto-preview">
            <?php if ($zakladni_udaje['cv_foto']): ?>
                <img src="<?= htmlspecialchars($zakladni_udaje['cv_foto']) ?>" alt="Profilová fotografie" style="max-width: 200px; border-radius: 8px;">
                <input type="hidden" name="cv_foto" value="<?= htmlspecialchars($zakladni_udaje['cv_foto']) ?>">
            <?php else: ?>
                <p>Žádná fotografie nebyla vybrána.</p>
                <input type="hidden" name="cv_foto" value="">
            <?php endif; ?>
        </div>

        <button type="button" id="blkt-vybrat-foto" class="btn btn-new-user">Vybrat fotografii</button>
    </div>
</form>

<!-- Modal pro výběr fotografie -->
<div id="blkt-foto-overlay" class="blkt-modal-overlay" style="display:none;"></div>
<div id="blkt-foto-modal" class="blkt-modal" style="display:none;max-width:600px;">
    <div class="blkt-modal-header">
        <h3>Vybrat profilovou fotografii</h3>
        <button class="blkt-modal-close">&times;</button>
    </div>
    <div class="blkt-modal-body">
        <div class="blkt-gallery-images"></div>
    </div>
    <div class="modal-actions">
        <button type="button" id="blkt-foto-cancel" class="btn btn-cancel">Zrušit</button>
    </div>
</div>