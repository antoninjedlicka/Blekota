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
            blkt_notifikace('Nastavení bylo úspěšně uloženo.', 'success');
          } else {
            blkt_notifikace('Chyba při ukládání: ' + j.error, 'error');
          }
        })
        .catch(err => {
          blkt_notifikace('Síťová chyba: ' + err.message, 'error');
        });
  });
}

// Spustíme po načtení tohoto skriptu
initNastaveniSection();