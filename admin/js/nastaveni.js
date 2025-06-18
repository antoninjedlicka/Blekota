// admin/js/nastaveni.js
// AJAX ukládání nastavení + výběr barvy s paletou

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

    // Inicializace výběru barvy
    initColorPicker(newForm);

    // AJAX odeslání formuláře
    newForm.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        console.log('Odesílám formulář přes AJAX');

        // Sestavíme FormData
        const formData = new FormData(this);

        // Debug - co odesíláme
        console.log('THEME hodnota:', formData.get('THEME'));

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
                    // Aplikujeme novou barvu na administraci
                    const newColor = document.getElementById('blkt-color-hex-input').value;
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
function initColorPicker(form) {
    const themeSelect = form.querySelector('#blkt-theme-select');
    const hexInput = form.querySelector('#blkt-color-hex-input');
    const colorPickerBtn = form.querySelector('#blkt-color-picker-btn');
    const colorPalette = document.getElementById('blkt-color-palette');
    const paletteClose = colorPalette.querySelector('.blkt-palette-close');
    const paletteCancel = colorPalette.querySelector('#blkt-palette-cancel');
    const paletteApply = colorPalette.querySelector('#blkt-palette-apply');
    const htmlColorPicker = colorPalette.querySelector('#blkt-html-color-picker');
    const previewBox = colorPalette.querySelector('#blkt-color-preview-box');

    let tempColor = hexInput.value;

    console.log('initColorPicker - počáteční barva:', tempColor);

    // Funkce pro kontrolu, zda je barva mezi přednastaveným
    function checkPresetColor(color) {
        const option = Array.from(themeSelect.options).find(opt =>
            opt.value !== 'custom' && opt.value === color
        );

        if (option) {
            themeSelect.value = color;
        } else {
            themeSelect.value = 'custom';
        }
    }

    // Funkce pro aktualizaci barvy tlačítka
    function updateButtonColor(color) {
        colorPickerBtn.style.backgroundColor = color;
        // Změníme barvu SVG ikony podle kontrastu
        const contrastColor = window.blkt_ziskej_kontrastni_barvu ?
            window.blkt_ziskej_kontrastni_barvu(color) : '#000000';
        colorPickerBtn.querySelector('svg').style.stroke = contrastColor;
    }

    // Funkce pro validaci hex barvy
    function isValidHex(hex) {
        return /^#[0-9A-F]{6}$/i.test(hex);
    }

    // Při změně v hex inputu
    hexInput.addEventListener('input', (e) => {
        let value = e.target.value;

        // Automaticky přidáme # pokud chybí
        if (value && !value.startsWith('#')) {
            value = '#' + value;
            hexInput.value = value;
        }

        // Převedeme na velká písmena
        value = value.toUpperCase();
        hexInput.value = value;

        // Pokud je validní hex, aktualizujeme
        if (isValidHex(value)) {
            updateButtonColor(value);
            checkPresetColor(value);

            // Okamžitá aplikace
            if (typeof window.blkt_aplikuj_barevne_schema === 'function') {
                window.blkt_aplikuj_barevne_schema(value);
            }
        }
    });

    // Při změně selectu
    themeSelect.addEventListener('change', (e) => {
        console.log('Select změna:', e.target.value);

        if (e.target.value !== 'custom') {
            const newColor = e.target.value;
            hexInput.value = newColor;
            updateButtonColor(newColor);

            // Okamžitá aplikace
            if (typeof window.blkt_aplikuj_barevne_schema === 'function') {
                window.blkt_aplikuj_barevne_schema(newColor);
            }
        }
    });

    // Otevření palety
    colorPickerBtn.addEventListener('click', (e) => {
        e.preventDefault();
        colorPalette.style.display = 'block';
        tempColor = hexInput.value;
        updatePreview(tempColor);
        htmlColorPicker.value = tempColor;
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
    colorPalette.querySelectorAll('.blkt-color-preset').forEach(btn => {
        btn.addEventListener('click', () => {
            tempColor = btn.dataset.color;
            updatePreview(tempColor);
            htmlColorPicker.value = tempColor;
        });
    });

    // HTML5 color picker
    htmlColorPicker.addEventListener('input', (e) => {
        tempColor = e.target.value.toUpperCase();
        updatePreview(tempColor);
    });

    // Aplikace vybrané barvy
    paletteApply.addEventListener('click', () => {
        console.log('Aplikuji barvu:', tempColor);

        // Nastavíme hodnoty
        hexInput.value = tempColor;
        updateButtonColor(tempColor);
        checkPresetColor(tempColor);

        colorPalette.style.display = 'none';

        // Okamžitý náhled v administraci
        if (typeof window.blkt_aplikuj_barevne_schema === 'function') {
            window.blkt_aplikuj_barevne_schema(tempColor);
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
        colorPalette.querySelectorAll('.blkt-color-preset').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.color === color);
        });
    }

    // Inicializace - nastavíme správnou barvu tlačítka
    updateButtonColor(hexInput.value);
    checkPresetColor(hexInput.value);
}

// Zajistíme, že se funkce spustí až po načtení DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNastaveniSection);
} else {
    // DOM je již načten
    initNastaveniSection();
}