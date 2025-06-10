// admin/js/nastaveni.js
// AJAX ukládání nastavení

function initNastaveniSection() {
    console.log('Inicializace sekce nastavení');

    const form = document.getElementById('blkt-form-nastaveni');
    if (!form) return;

    // AJAX odeslání formuláře
    form.addEventListener('submit', e => {
        e.preventDefault();

        // Zobrazíme indikátor načítání
        const saveBtn = document.querySelector('.blkt-sticky-save button');
        const originalText = saveBtn.textContent;
        saveBtn.textContent = 'Ukládám...';
        saveBtn.disabled = true;

        // Odešleme data
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form)
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    blkt_notifikace('Nastavení bylo úspěšně uloženo.', 'success');
                } else {
                    blkt_notifikace('Chyba při ukládání: ' + data.error, 'error');
                }
            })
            .catch(error => {
                blkt_notifikace('Síťová chyba: ' + error.message, 'error');
            })
            .finally(() => {
                // Obnovíme tlačítko
                saveBtn.textContent = originalText;
                saveBtn.disabled = false;
            });
    });

    // Live náhled barevného schématu (volitelné)
    const themeSelect = form.querySelector('select[name="THEME"]');
    if (themeSelect) {
        themeSelect.addEventListener('change', function() {
            // Můžete zde přidat náhled barvy
            console.log('Vybrané téma:', this.value);
        });
    }
}

// Spustíme inicializaci
initNastaveniSection();