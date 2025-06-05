<?php
// install.php
// Instalační průvodce - bez reloadu, přepínání přes JS
$default_www = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
$default_blog = $default_www . "/blog";
?>
<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Instalace webu</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/install.css">
  <script defer src="js/install.js"></script> <!-- Připojení JavaScriptu -->
</head>
<body>

<div class="wizard">

  <!-- Krok 1: Vítejte -->
  <div class="krok active">
    <h2>Vítejte v instalátoru webu!</h2>
    <h3>Ahoj, já jsem Tonda!</h3>
    <p>Vytvořil jsem pro Vás toto prostředí pro jednoduché nasazení na Vašem hostingu s možností okamžitého publikování myšlenek.</p>
    <p>Tímto průvodcem provedete instalaci prostředí.</p>
    <img src="media/autor.png" alt="Autor instalátoru" class="author-img">
    <button class="btn btn-next">Další</button>
  </div>

  <!-- Krok 2: Přístup k databázi -->
  <div class="krok">
    <h2>Přístupové údaje k databázi</h2>
    <form autocomplete="off">
      <input type="hidden" name="step" value="2">
      <input style="opacity:0;position:absolute" autocomplete="off">
      <input type="password" style="opacity:0;position:absolute" autocomplete="new-password">

      <div class="blkt-formular-skupina">
        <input name="host" id="host" placeholder=" " required>
        <label for="host">Host</label>
      </div>
      <div class="blkt-formular-skupina">
        <input name="dbname" id="dbname" placeholder=" " required>
        <label for="dbname">Databáze</label>
      </div>
      <div class="blkt-formular-skupina">
        <input name="user" id="user" placeholder=" " required>
        <label for="user">Uživatel</label>
      </div>
      <div class="blkt-formular-skupina">
        <input type="password" name="pass" id="pass" placeholder=" " readonly onfocus="this.removeAttribute('readonly');">
        <label for="pass">Heslo</label>
      </div>

      <button type="submit" class="btn btn-next">Další</button>
    </form>
  </div>

  <!-- Krok 3: Reset tabulek -->
  <div class="krok">
    <h2>Reset tabulek</h2>
    <p>Existující tabulky budou odstraněny a poté vytvořeny znovu.</p>
    <ul class="icon-list">
      <li>Seznam uživatelů</li>
      <li>Konfigurace webu</li>
      <li>Příspěvky a jejich detaily na blogu</li>
      <li>Databáze obrázků</li>
    </ul>
    <form>
      <input type="hidden" name="step" value="3">
      <button type="submit" class="btn btn-next">Další</button>
    </form>
  </div>

  <!-- Krok 4: Konfigurace webu -->
  <div class="krok">
    <h2>Konfigurace webu</h2>
    <form autocomplete="off">
      <input type="hidden" name="step" value="4">
      <input style="opacity:0;position:absolute" autocomplete="off">
      <input type="password" style="opacity:0;position:absolute" autocomplete="new-password">

<div class="blkt-formular-skupina">
  <input name="www" id="www" placeholder=" " required value="<?= htmlspecialchars($default_www) ?>">
  <label for="www">Webová adresa webu</label>
</div>
<div class="blkt-formular-skupina">
  <input name="blog" id="blog" placeholder=" " required value="<?= htmlspecialchars($default_blog) ?>">
  <label for="blog">Webová adresa blogu</label>
</div>
      <div class="blkt-formular-skupina">
        <select name="theme" id="theme" required>
          <option value="" disabled selected hidden></option>
          <option value="#ff0000">Červená</option>
          <option value="#0000ff">Modrá</option>
          <option value="#00ff00">Zelená</option>
        </select>
        <label for="theme">Barevné schéma webu</label>
      </div>

      <button type="submit" class="btn btn-next">Další</button>
    </form>
  </div>

  <!-- Krok 5: Vytvoření administrátora -->
  <div class="krok">
    <h2>Vytvoření administrátora</h2>
    <form autocomplete="off">
      <input type="hidden" name="step" value="5">
      <input style="opacity:0;position:absolute" autocomplete="off">
      <input type="password" style="opacity:0;position:absolute" autocomplete="new-password">

      <div class="blkt-formular-skupina">
        <input name="jmeno" id="jmeno" placeholder=" " required>
        <label for="jmeno">Jméno</label>
      </div>
      <div class="blkt-formular-skupina">
        <input name="prijmeni" id="prijmeni" placeholder=" " required>
        <label for="prijmeni">Příjmení</label>
      </div>
      <div class="blkt-formular-skupina">
        <input type="email" name="mail" id="mail" placeholder=" " required>
        <label for="mail">E-mail</label>
      </div>
      <div class="blkt-formular-skupina">
        <input type="password" name="heslo" id="password" placeholder=" " readonly onfocus="this.removeAttribute('readonly');" required>
        <label for="password">Heslo</label>
      </div>
      <div class="blkt-formular-skupina">
        <input type="password" name="heslo_confirm" id="password_confirm" placeholder=" " readonly onfocus="this.removeAttribute('readonly');" required>
        <label for="password_confirm">Heslo znovu</label>
      </div>

      <button type="submit" class="btn btn-next" id="nextBtn">Další</button>
    </form>
  </div>

  <!-- Krok 6: Hotovo -->
  <div class="krok">
    <h2>Hotovo!</h2>
    <p>Instalace úspěšně dokončena. <a href="index.php">Přejít na web</a></p>
    <form>
      <input type="hidden" name="dokoncit" value="1">
      <button type="submit" class="btn btn-next">Dokončit instalaci</button>
    </form>
  </div>

  <!-- Procentuální průběh -->
  <img src="media/fi-br-percent-0.svg" alt="0 %" class="percent-icon" id="progress-icon" aria-hidden="true">

</div>

</body>
</html>
