<?php
// admin/content/nastaveni/prehled.php
// Původní HTML formulář pro základní nastavení webu
?>
<form action="action/save_nastaveni.php" method="post" class="nastaveni-form">
    <div class="blkt-admin-box">

    <h2>Nastavení webu</h2>

  <div class="blkt-formular-skupina">
    <input type="text" name="www" placeholder=" " required>
    <label for="www">Webová adresa</label>
  </div>

  <div class="blkt-formular-skupina">
    <input type="text" name="blog" placeholder=" " required>
    <label for="blog">Adresa blogu</label>
  </div>

  <div class="blkt-formular-skupina">
    <select name="theme" required>
      <option value="" disabled selected hidden></option>
      <option value="#ff0000">Červená</option>
      <option value="#0000ff">Modrá</option>
      <option value="#00ff00">Zelená</option>
    </select>
    <label for="theme">Barevné schéma</label>
  </div>
    </div>
</form>
