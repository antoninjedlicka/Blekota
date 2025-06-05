// admin/js/nastaveni.js
// AJAX uložení formuláře "Nastavení"

function initNastaveniSection() {
  const form = document.querySelector('.nastaveni-form');
  if (!form) return;

  form.addEventListener('submit', e => {
    e.preventDefault();
    // Odešleme FormData fetchem
    fetch(form.action, {
      method: 'POST',
      body: new FormData(form)
    })
    .then(r => {
      // Očekáváme JSON { status:'ok' } nebo { status:'error', error:'...' }
      return r.json();
    })
    .then(j => {
      if (j.status === 'ok') {
        alert('Nastavení bylo úspěšně uloženo.');
      } else {
        alert('Chyba při ukládání: ' + j.error);
      }
    })
    .catch(err => {
      alert('Síťová chyba: ' + err.message);
    });
  });
}

// Spustíme po načtení tohoto skriptu
initNastaveniSection();
