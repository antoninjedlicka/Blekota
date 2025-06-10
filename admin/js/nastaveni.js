// admin/js/nastaveni.js
// AJAX ukládání nastavení

function initNastaveniSection() {
    console.log('Inicializace sekce nastavení');

    const form = document.getElementById('blkt-form-nastaveni');
    if (!form) {
        console.log('Formulář nastavení nenalezen');
        return;
    }

    // Odstraníme případné existující event listenery
    const newForm = form.cloneNode(true);
    form.parentNode.replaceChild(newForm, form);

    // AJAX odeslání formuláře
    newForm.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        console.log('Odesílám formulář přes AJAX');

        // Sestavíme FormData
        const formData = new FormData(this);

        // Odešleme data
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Síťová chyba');
                }
                return response.json();
            })
            .then(data => {
                console.log('Odpověď serveru:', data);
                if (data.status === 'ok') {
                    blkt_notifikace('Nastavení bylo úspěšně uloženo.', 'success');
                } else {
                    blkt_notifikace('Chyba při ukládání: ' + (data.error || 'Neznámá chyba'), 'error');
                }
            })
            .catch(error => {
                console.error('Chyba:', error);
                blkt_notifikace('Síťová chyba: ' + error.message, 'error');
            });

        return false;
    });

    // Live náhled barevného schématu (volitelné)
    const themeSelect = newForm.querySelector('select[name="THEME"]');
    if (themeSelect) {
        themeSelect.addEventListener('change', function() {
            console.log('Vybrané téma:', this.value);
            // Zde můžete přidat vizuální náhled tématu
            // Například změna barvy nějakého elementu na stránce
        });
    }
}

// Zajistíme, že se funkce spustí až po načtení DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNastaveniSection);
} else {
    // DOM je již načten
    initNastaveniSection();
}