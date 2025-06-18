// admin/js/nastaveni.js
// AJAX ukládání nastavení + výběr barvy s paletou

function initNastaveniSection() {
    console.log('Inicializace sekce nastavení');

    const form = document.getElementById('blkt-form-nastaveni');
    if (!form) {
        console.log('Formulář nastavení nenalezen');
        return;
    }

    // Inicializace výběru barvy
    initColorPicker();

    // AJAX odeslání formuláře
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        console.log('Odesílám formulář přes AJAX');

        // Sestavíme FormData
        const formData = new FormData(form);

        // Debug - co odesíláme
        console.log('THEME hodnota:', formData.get('THEME'));

        // Odešleme data
        fetch(form.action, {
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
                    // Aplikujeme novou barvu na administraci
                    const newColor = document.getElementById('blkt-theme-value').value;
                    if (typeof window.blkt_aplikuj_barevne_schema === 'function') {
                        window.blkt_aplikuj_barevne_schema(newColor);
                    }
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
}

// Funkce pro inicializaci výběru barvy
function initColorPicker() {
    const themeSelect = document.getElementById('blkt-theme-select');
    const themeValue = document.getElementById('blkt-theme-value');
    const colorPickerBtn = document.getElementById('blkt-color-picker-btn');
    const colorPreview = document.getElementById('blkt-color-preview');
    const colorPalette = document.getElementById('blkt-color-palette');
    const paletteClose = document.querySelector('.blkt-palette-close');
    const paletteCancel = document.getElementById('blkt-palette-cancel');
    const paletteApply = document.getElementById('blkt-palette-apply');
    const htmlColorPicker = document.getElementById('blkt-html-color-picker');
    const colorHexInput = document.getElementById('blkt-color-hex-input');
    const previewBox = document.getElementById('blkt-color-preview-box');

    let tempColor = themeValue.value;

    console.log('initColorPicker - počáteční barva:', tempColor);

    // Otevření palety
    colorPickerBtn.addEventListener('click', (e) => {
        e.preventDefault();
        colorPalette.style.display = 'block';
        tempColor = themeValue.value;
        updatePreview(tempColor);
    });

    // Zavření palety
    [paletteClose, paletteCancel].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', () => {
                colorPalette.style.display = 'none';
            });
        }
    });

    // Kliknutí mimo paletu ji zavře
    document.addEventListener('click', (e) => {
        if (!colorPalette.contains(e.target) && !colorPickerBtn.contains(e.target)) {
            colorPalette.style.display = 'none';
        }
    });

    // Kliknutí na přednastavenou barvu
    document.querySelectorAll('.blkt-color-preset').forEach(btn => {
        btn.addEventListener('click', () => {
            tempColor = btn.dataset.color;
            updatePreview(tempColor);
            htmlColorPicker.value = tempColor;
            colorHexInput.value = tempColor;
        });
    });

    // HTML5 color picker
    htmlColorPicker.addEventListener('input', (e) => {
        tempColor = e.target.value;
        updatePreview(tempColor);
        colorHexInput.value = tempColor;
    });

    // Hex input
    colorHexInput.addEventListener('input', (e) => {
        let hex = e.target.value;
        // Přidáme # pokud chybí
        if (hex && !hex.startsWith('#')) {
            hex = '#' + hex;
        }
        if (/^#[0-9A-F]{6}$/i.test(hex)) {
            tempColor = hex;
            updatePreview(tempColor);
            htmlColorPicker.value = hex;
        }
    });

    // Aplikace vybrané barvy
    paletteApply.addEventListener('click', () => {
        console.log('Aplikuji barvu:', tempColor);

        // Nastavíme hodnoty
        themeValue.value = tempColor;
        colorPreview.style.backgroundColor = tempColor;

        // Aktualizujeme select
        const presetOption = Array.from(themeSelect.options).find(opt => opt.value === tempColor);
        if (presetOption) {
            themeSelect.value = tempColor;
        } else {
            themeSelect.value = 'custom';
        }

        colorPalette.style.display = 'none';

        // Okamžitý náhled v administraci
        if (typeof window.blkt_aplikuj_barevne_schema === 'function') {
            window.blkt_aplikuj_barevne_schema(tempColor);
        }
    });

    // Změna v selectu
    themeSelect.addEventListener('change', (e) => {
        console.log('Select změna:', e.target.value);

        if (e.target.value !== 'custom') {
            const newColor = e.target.value;
            themeValue.value = newColor;
            colorPreview.style.backgroundColor = newColor;

            // Okamžitá aplikace
            if (typeof window.blkt_aplikuj_barevne_schema === 'function') {
                window.blkt_aplikuj_barevne_schema(newColor);
            }
        }
    });

    // Funkce pro aktualizaci náhledu
    function updatePreview(color) {
        previewBox.style.backgroundColor = color;

        // Získáme kontrastní barvu pro text
        if (typeof window.blkt_ziskej_kontrastni_barvu === 'function') {
            previewBox.style.color = window.blkt_ziskej_kontrastni_barvu(color);
        }

        // Označíme aktivní preset
        document.querySelectorAll('.blkt-color-preset').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.color === color);
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