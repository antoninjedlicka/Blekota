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
                    const newColor = document.getElementById('blkt-theme-value').value;
                    blkt_aplikuj_barevne_schema(newColor);
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
    const themeValue = form.querySelector('#blkt-theme-value');
    const colorPickerBtn = form.querySelector('#blkt-color-picker-btn');
    const colorPreview = form.querySelector('.blkt-color-preview');
    const colorPalette = form.querySelector('#blkt-color-palette');
    const paletteClose = form.querySelector('.blkt-palette-close');
    const paletteCancel = form.querySelector('#blkt-palette-cancel');
    const paletteApply = form.querySelector('#blkt-palette-apply');
    const htmlColorPicker = form.querySelector('#blkt-html-color-picker');
    const colorHexInput = form.querySelector('#blkt-color-hex-input');
    const previewBox = form.querySelector('#blkt-color-preview-box');

    let tempColor = themeValue.value;

    // Otevření palety
    colorPickerBtn.addEventListener('click', () => {
        colorPalette.style.display = 'block';
        tempColor = themeValue.value;
        updatePreview(tempColor);
    });

    // Zavření palety
    [paletteClose, paletteCancel].forEach(btn => {
        btn.addEventListener('click', () => {
            colorPalette.style.display = 'none';
        });
    });

    // Kliknutí na přednastavenou barvu
    form.querySelectorAll('.blkt-color-preset').forEach(btn => {
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
        const hex = e.target.value;
        if (/^#[0-9A-F]{6}$/i.test(hex)) {
            tempColor = hex;
            updatePreview(tempColor);
            htmlColorPicker.value = hex;
        }
    });

    // Aplikace vybrané barvy
    paletteApply.addEventListener('click', () => {
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
        blkt_aplikuj_barevne_schema(tempColor);
    });

    // Změna v selectu
    themeSelect.addEventListener('change', (e) => {
        if (e.target.value !== 'custom') {
            const newColor = e.target.value;
            themeValue.value = newColor;
            colorPreview.style.backgroundColor = newColor;
            blkt_aplikuj_barevne_schema(newColor);
        }
    });

    // Funkce pro aktualizaci náhledu
    function updatePreview(color) {
        previewBox.style.backgroundColor = color;
        previewBox.style.color = blkt_ziskej_kontrastni_barvu(color);

        // Označíme aktivní preset
        form.querySelectorAll('.blkt-color-preset').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.color === color);
        });
    }
}

// Funkce pro aplikaci barevného schématu na administraci
function blkt_aplikuj_barevne_schema(color) {
    // Vytvoříme nebo aktualizujeme style tag
    let styleTag = document.getElementById('blkt-dynamic-theme');
    if (!styleTag) {
        styleTag = document.createElement('style');
        styleTag.id = 'blkt-dynamic-theme';
        document.head.appendChild(styleTag);
    }

    // Generujeme odstíny barvy
    const shades = blkt_generuj_odstiny(color);

    // CSS proměnné pro dynamické téma
    styleTag.textContent = `
        :root {
            --blkt-primary: ${shades.primary};
            --blkt-primary-dark: ${shades.dark};
            --blkt-primary-light: ${shades.light};
            --blkt-primary-lighter: ${shades.lighter};
            --blkt-primary-shadow: ${shades.shadow};
        }
        
        /* Přepsání výchozích barev */
        .menu-item.active,
        .blkt-tabs button.active::after,
        .dashboard-stats li,
        h2, h3, h4, h5, h6,
        .blkt-cv-pozice-header h4,
        .month-divider,
        a {
            color: var(--blkt-primary) !important;
        }
        
        .menu-item.active {
            background: linear-gradient(135deg, var(--blkt-primary), var(--blkt-primary-light)) !important;
        }
        
        .btn-new-user, .btn-edit-user, .btn-new-post,
        .blkt-tlacitko-novy,
        button:not(.btn-cancel):not(.btn-delete-user):not(.blkt-tlacitko-smazat):not(.blkt-odebrat-radek):not(.blkt-galerie-odebrat):not(.blkt-modal-close):not(.blkt-tabs button):not(.blkt-color-preset) {
            background: linear-gradient(135deg, var(--blkt-primary), var(--blkt-primary-light)) !important;
        }
        
        button:not(.btn-cancel):not(.btn-delete-user):not(.blkt-tlacitko-smazat):not(.blkt-odebrat-radek):not(.blkt-galerie-odebrat):not(.blkt-modal-close):not(.blkt-tabs button):not(.blkt-color-preset):hover {
            background: linear-gradient(135deg, var(--blkt-primary-dark), var(--blkt-primary)) !important;
            box-shadow: 0 6px 20px var(--blkt-primary-shadow) !important;
        }
        
        input:focus, select:focus, textarea:focus {
            border-color: var(--blkt-primary) !important;
        }
        
        input:focus + label,
        select:focus + label,
        textarea:focus + label,
        input:not(:placeholder-shown) + label,
        textarea:not(:placeholder-shown) + label {
            color: var(--blkt-primary) !important;
        }
        
        .blkt-tabs button.active {
            color: var(--blkt-primary) !important;
        }
        
        .blkt-tabs button::after,
        .blkt-tabs button.active::after {
            background: linear-gradient(90deg, var(--blkt-primary), var(--blkt-primary-light)) !important;
        }
        
        .blkt-upload-zone {
            border-color: var(--blkt-primary) !important;
            background: linear-gradient(135deg, var(--blkt-primary-lighter), transparent) !important;
        }
        
        .blkt-gallery-thumb:hover,
        .blkt-gallery-thumb-modal:hover {
            border-color: var(--blkt-primary) !important;
        }
        
        .menu-item::before {
            background: var(--blkt-primary) !important;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--blkt-primary), var(--blkt-primary-light)) !important;
        }
        
        ::selection {
            background: var(--blkt-primary) !important;
        }
        
        ::-moz-selection {
            background: var(--blkt-primary) !important;
        }
    `;
}

// Pomocné funkce pro práci s barvami
function blkt_generuj_odstiny(color) {
    // Převod hex na RGB
    const hex2rgb = (hex) => {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    };

    // Převod RGB na hex
    const rgb2hex = (r, g, b) => {
        return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    };

    const rgb = hex2rgb(color);
    if (!rgb) return {
        primary: color,
        dark: color,
        light: color,
        lighter: color,
        shadow: 'rgba(0,0,0,0.3)'
    };

    // Tmavší odstín (80% původní barvy)
    const dark = rgb2hex(
        Math.floor(rgb.r * 0.8),
        Math.floor(rgb.g * 0.8),
        Math.floor(rgb.b * 0.8)
    );

    // Světlejší odstín (směs s bílou)
    const light = rgb2hex(
        Math.min(255, Math.floor(rgb.r + (255 - rgb.r) * 0.3)),
        Math.min(255, Math.floor(rgb.g + (255 - rgb.g) * 0.3)),
        Math.min(255, Math.floor(rgb.b + (255 - rgb.b) * 0.3))
    );

    // Ještě světlejší pro pozadí
    const lighter = rgb2hex(
        Math.min(255, Math.floor(rgb.r + (255 - rgb.r) * 0.9)),
        Math.min(255, Math.floor(rgb.g + (255 - rgb.g) * 0.9)),
        Math.min(255, Math.floor(rgb.b + (255 - rgb.b) * 0.9))
    );

    // Stín
    const shadow = `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.4)`;

    return {
        primary: color,
        dark: dark,
        light: light,
        lighter: lighter,
        shadow: shadow
    };
}

// Funkce pro získání kontrastní barvy (černá/bílá)
function blkt_ziskej_kontrastni_barvu(color) {
    const hex = color.replace('#', '');
    const r = parseInt(hex.substr(0, 2), 16);
    const g = parseInt(hex.substr(2, 2), 16);
    const b = parseInt(hex.substr(4, 2), 16);
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    return luminance > 0.5 ? '#000000' : '#ffffff';
}