// js/install.js
// Instalační průvodce - kompletní logika přepínání, validace, ajax

document.addEventListener('DOMContentLoaded', () => {
  const kroky = document.querySelectorAll('.krok');
  let aktualniKrok = 0;
  const progressIcon = document.getElementById('progress-icon');

const showKrok = (index) => {
  const aktualni = document.querySelector('.krok.active');
  const novy = kroky[index];

  if (!novy) return;

  fadeOut(aktualni, () => {
    aktualni.classList.remove('active');
    novy.style.display = 'block';
    novy.style.opacity = 0;
    fadeIn(novy);
    novy.classList.add('active');
  });

  // Změna ikony postupu
  if (progressIcon) {
    progressIcon.src = 'media/fi-br-percent-' + (index * 20) + '.svg';
    progressIcon.alt = (index * 20) + ' %';
  }
};


  function fadeIn(element) {
    element.style.display = 'block';
    element.style.opacity = 0;
    (function fade() {
      let val = parseFloat(element.style.opacity);
      if (!((val += 0.1) > 1)) {
        element.style.opacity = val;
        requestAnimationFrame(fade);
      }
    })();
  }

function fadeOut(element, callback) {
  if (!element) {
    if (callback) callback();
    return;
  }
  element.style.opacity = 1;
  (function fade() {
    if ((element.style.opacity -= 0.1) < 0) {
      element.style.display = 'none';
      if (callback) callback();
    } else {
      requestAnimationFrame(fade);
    }
  })();
}

  function showError(form, message) {
    clearError(form);
    const error = document.createElement('div');
    error.className = 'error-message';
    error.textContent = message;
    form.appendChild(error);
  }

  function clearError(form) {
    const oldError = form.querySelector('.error-message');
    if (oldError) oldError.remove();
  }

  async function ajaxPost(url, data, form) {
    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(data)
      });
      const json = await response.json();
      if (json.status === 'ok') {
aktualniKrok++;
if (aktualniKrok >= kroky.length) {
  window.location.href = 'index.php'; // Přesměrování na hlavní stránku
} else {
  showKrok(aktualniKrok);
}

      } else if (json.error) {
        showError(form, json.error);
      } else {
        showError(form, 'Neznámá chyba serveru.');
      }
    } catch (err) {
      showError(form, 'Chyba spojení se serverem.');
    }
  }

  document.querySelectorAll('.btn-next').forEach(button => {
    button.addEventListener('click', (e) => {
      e.preventDefault();
      const form = button.closest('form');
      if (!form) {
        // Pokud není form, jen posuň krok
        aktualniKrok++;
        showKrok(aktualniKrok);
        return;
      }

      clearError(form);

      // Validace polí
      const inputs = form.querySelectorAll('input[required], select[required]');
      let valid = true;
      inputs.forEach(input => {
        if (!input.value.trim()) {
          valid = false;
          showError(form, 'Vyplňte prosím všechna povinná pole.');
        }
      });
      if (!valid) return;

      // Odeslání podle kroku
      const step = form.querySelector('input[name="step"]')?.value;
      if (step === '2') {
        // Připojení k DB
        const data = {
          host: form.host.value,
          dbname: form.dbname.value,
          user: form.user.value,
          pass: form.pass.value
        };
        ajaxPost('install/db_connect.php', data, form);
      } else if (step === '3') {
        // Reset tabulek
        ajaxPost('install/db_tables.php', {}, form);
      } else if (step === '4') {
        // Uložení konfigurace
        const data = {
          www: form.www.value,
          blog: form.blog.value,
          theme: form.theme.value,
          new_web: 'true'
        };
        ajaxPost('install/db_config.php', data, form);
      } else if (step === '5') {
        // Vložení admina
        const data = {
          jmeno: form.jmeno.value,
          prijmeni: form.prijmeni.value,
          mail: form.mail.value,
          heslo: form.password.value,
          heslo_confirm: form.password_confirm.value
        };
        ajaxPost('install/db_admin.php', data, form);
      } else {
        // Hotovo
        ajaxPost('install/db_finish.php', {}, form);
      }
    });
  });

  // Síla hesla
  const passwordField = document.getElementById('password');
  if (passwordField) {
    const strengthIndicator = document.createElement('div');
    strengthIndicator.id = 'strength-indicator';
    strengthIndicator.style.marginTop = '5px';
    strengthIndicator.style.fontSize = '0.9em';
    strengthIndicator.style.fontWeight = '500';
    passwordField.parentNode.appendChild(strengthIndicator);

    passwordField.addEventListener('input', () => {
      const val = passwordField.value;
      const strength = getPasswordStrength(val);
      strengthIndicator.textContent = strength.text;
      strengthIndicator.style.color = strength.color;
    });
  }

  function getPasswordStrength(password) {
    let score = 0;
    if (!password) return { text: '', color: '' };
    if (password.length >= 8) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;

    if (score <= 2) return { text: 'Slabé heslo', color: 'red' };
    if (score === 3 || score === 4) return { text: 'Střední heslo', color: 'orange' };
    if (score === 5) return { text: 'Silné heslo', color: 'green' };

    return { text: '', color: '' };
  }
});
