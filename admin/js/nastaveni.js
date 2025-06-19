// admin/js/nastaveni.js
// AJAX ukládání nastavení + výběr barvy s paletou

function initNastaveniSection() {
    const form = document.querySelector('.nastaveni-form');
    if (!form) return;

    // Inicializace výběru barvy
    initColorPicker();

    // AJAX odeslání formuláře - stejně jako u homepage
    form.addEventListener('submit', e => {
        e.preventDefault();
        const fd = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            body: fd
        })
            .then(r => r.json())
            .then(j => {
                if (j.status === 'ok') {
                    blkt_notifikace('Nastavení bylo úspěšně uloženo.', 'success');
                    // Aplikujeme novou barvu
                    const newColor = document.getElementById('blkt-color-hex-input').value;
                    if (typeof window.blkt_aplikuj_barevne_schema === 'function') {
                        window.blkt_aplikuj_barevne_schema(newColor);
                    }
                } else {
                    blkt_notifikace('Chyba při ukládání: ' + j.error, 'error');
                }
            })
            .catch(err => {
                blkt_notifikace('Síťová chyba: ' + err.message, 'error');
            });
    });
}

// Funkce pro inicializaci výběru barvy
function initColorPicker() {
    const themeSelect = document.querySelector('#blkt-theme-select');
    const hexInput = document.querySelector('#blkt-color-hex-input');
    const colorPickerBtn = document.querySelector('#blkt-color-picker-btn');
    const colorPalette = document.getElementById('blkt-color-palette');

    if (!themeSelect || !hexInput || !colorPickerBtn || !colorPalette) return;

    let tempColor = hexInput.value;

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

        if (value && !value.startsWith('#')) {
            value = '#' + value;
            hexInput.value = value;
        }

        value = value.toUpperCase();
        hexInput.value = value;

        if (isValidHex(value)) {
            updateButtonColor(value);
            checkPresetColor(value);

            if (typeof window.blkt_aplikuj_barevne_schema === 'function') {
                window.blkt_aplikuj_barevne_schema(value);
            }
        }
    });

    // Při změně selectu
    themeSelect.addEventListener('change', (e) => {
        if (e.target.value !== 'custom') {
            const newColor = e.target.value;
            hexInput.value = newColor;
            updateButtonColor(newColor);

            if (typeof window.blkt_aplikuj_barevne_schema === 'function') {
                window.blkt_aplikuj_barevne_schema(newColor);
            }
        }
    });

    // Výběr barvy z palety - NOVÁ IMPLEMENTACE
    colorPickerBtn.addEventListener('click', (e) => {
        e.preventDefault();
        colorPalette.style.display = 'block';
        tempColor = hexInput.value;

        const htmlColorPicker = colorPalette.querySelector('#blkt-html-color-picker');
        const previewBox = colorPalette.querySelector('#blkt-color-preview-box');

        if (htmlColorPicker) htmlColorPicker.value = tempColor;
        updatePreview(tempColor);
    });

    // Funkce pro aktualizaci náhledu
    function updatePreview(color) {
        const previewBox = colorPalette.querySelector('#blkt-color-preview-box');
        if (previewBox) {
            previewBox.style.backgroundColor = color;
            if (typeof window.blkt_ziskej_kontrastni_barvu === 'function') {
                previewBox.style.color = window.blkt_ziskej_kontrastni_barvu(color);
            }
        }

        // Označíme aktivní preset
        colorPalette.querySelectorAll('.blkt-color-preset').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.color === color);
        });
    }

    // Event handlery pro paletu
    const paletteClose = colorPalette.querySelector('.blkt-palette-close');
    const paletteCancel = colorPalette.querySelector('#blkt-palette-cancel');
    const paletteApply = colorPalette.querySelector('#blkt-palette-apply');
    const htmlColorPicker = colorPalette.querySelector('#blkt-html-color-picker');

    // Zavření palety
    if (paletteClose) {
        paletteClose.addEventListener('click', () => {
            colorPalette.style.display = 'none';
        });
    }

    if (paletteCancel) {
        paletteCancel.addEventListener('click', () => {
            colorPalette.style.display = 'none';
        });
    }

    // Kliknutí mimo paletu ji zavře
    document.addEventListener('click', (e) => {
        if (colorPalette.style.display === 'block' &&
            !colorPalette.contains(e.target) &&
            !colorPickerBtn.contains(e.target)) {
            colorPalette.style.display = 'none';
        }
    });

    // Kliknutí na přednastavenou barvu
    colorPalette.querySelectorAll('.blkt-color-preset').forEach(btn => {
        btn.addEventListener('click', () => {
            tempColor = btn.dataset.color;
            updatePreview(tempColor);
            if (htmlColorPicker) htmlColorPicker.value = tempColor;
        });
    });

    // HTML5 color picker
    if (htmlColorPicker) {
        htmlColorPicker.addEventListener('input', (e) => {
            tempColor = e.target.value.toUpperCase();
            updatePreview(tempColor);
        });
    }

    // Aplikace vybrané barvy
    if (paletteApply) {
        paletteApply.addEventListener('click', () => {
            hexInput.value = tempColor;
            updateButtonColor(tempColor);
            checkPresetColor(tempColor);
            colorPalette.style.display = 'none';

            // Okamžitá aplikace
            if (typeof window.blkt_aplikuj_barevne_schema === 'function') {
                window.blkt_aplikuj_barevne_schema(tempColor);
            }
        });
    }

    // Inicializace
    updateButtonColor(hexInput.value);
    checkPresetColor(hexInput.value);
}

// Spuštění
initNastaveniSection();