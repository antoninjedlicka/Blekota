// admin/js/nastaveni.js
console.log('nastaveni.js - soubor načten');

function initNastaveniSection() {
    console.log('=== initNastaveniSection START ===');

    const form = document.getElementById('blkt-form-nastaveni');
    if (!form) {
        console.error('CHYBA: Formulář nenalezen!');
        return;
    }

    // AJAX SUBMIT - oprava ukládání
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submit - preventDefault');

        const formData = new FormData(form);

        // Zajistíme, že se odešle pouze hodnota z hex inputu pro THEME
        const hexInput = document.getElementById('blkt-color-hex-input');
        if (hexInput) {
            formData.set('THEME', hexInput.value);
        }

        // Debug
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }

        // Přidáme header pro AJAX
        fetch('action/save_nastaveni.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
            .then(r => {
                console.log('Response status:', r.status);
                if (!r.ok) {
                    throw new Error('HTTP error! status: ' + r.status);
                }
                return r.text();
            })
            .then(text => {
                console.log('Response text:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed data:', data);

                    if (typeof window.blkt_notifikace === 'function') {
                        if (data.status === 'ok') {
                            window.blkt_notifikace('Nastavení bylo úspěšně uloženo.', 'success');

                            // Aplikovat barvu
                            const newColor = hexInput.value;
                            if (typeof window.blkt_aplikuj_barevne_schema === 'function') {
                                window.blkt_aplikuj_barevne_schema(newColor);
                            }
                        } else {
                            window.blkt_notifikace('Chyba: ' + data.error, 'error');
                        }
                    } else {
                        alert(data.status === 'ok' ? 'Uloženo!' : 'Chyba!');
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Response was:', text);
                    if (typeof window.blkt_notifikace === 'function') {
                        window.blkt_notifikace('Chyba při zpracování odpovědi serveru', 'error');
                    } else {
                        alert('Chyba při zpracování odpovědi serveru');
                    }
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                if (typeof window.blkt_notifikace === 'function') {
                    window.blkt_notifikace('Chyba při ukládání: ' + err.message, 'error');
                } else {
                    alert('Chyba při ukládání!');
                }
            });
    });

    // Prvky pro práci s barvami
    const hexInput = document.getElementById('blkt-color-hex-input');
    const colorBtn = document.getElementById('blkt-color-picker-btn');
    const themeSelect = document.getElementById('blkt-theme-select');
    const palette = document.getElementById('blkt-color-palette');

    // ZMĚNA BARVY V HEX INPUTU
    if (hexInput) {
        hexInput.addEventListener('input', function() {
            let value = this.value.trim();

            // Automaticky přidat # pokud chybí
            if (value && !value.startsWith('#')) {
                value = '#' + value;
                this.value = value;
            }

            // Validace hex formátu
            if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
                // Aktualizovat tlačítko
                if (colorBtn) {
                    colorBtn.style.backgroundColor = value;
                }

                // Aktualizovat select
                if (themeSelect) {
                    const hasOption = Array.from(themeSelect.options).some(opt => opt.value === value);
                    themeSelect.value = hasOption ? value : 'custom';
                }

                // Okamžitá aplikace na rozhraní
                if (typeof window.blkt_aplikuj_barevne_schema === 'function') {
                    window.blkt_aplikuj_barevne_schema(value);
                }
            }
        });
    }

    // ZMĚNA SELECTU
    if (themeSelect) {
        themeSelect.addEventListener('change', function() {
            if (this.value !== 'custom' && hexInput) {
                hexInput.value = this.value;

                // Aktualizovat tlačítko
                if (colorBtn) {
                    colorBtn.style.backgroundColor = this.value;
                }

                // Spustit událost input pro aktualizaci
                hexInput.dispatchEvent(new Event('input'));
            }
        });
    }

    // OTEVŘENÍ PALETY
    if (colorBtn) {
        colorBtn.addEventListener('click', function(e) {
            e.preventDefault();

            if (palette) {
                palette.style.display = 'block';

                // Nastavit aktuální barvu do palety
                const currentColor = hexInput.value;
                const htmlPicker = document.getElementById('blkt-html-color-picker');
                if (htmlPicker) {
                    htmlPicker.value = currentColor;
                }

                // Aktualizovat náhled
                blkt_aktualizuj_nahled_palety(currentColor);
            }
        });
    }

    // PRÁCE S PALETOU
    if (palette) {
        let tempColor = hexInput ? hexInput.value : '#3498db';

        // Zavřít tlačítka
        const closeBtn = palette.querySelector('.blkt-palette-close');
        const cancelBtn = document.getElementById('blkt-palette-cancel');

        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                palette.style.display = 'none';
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                palette.style.display = 'none';
            });
        }

        // Aplikovat barvu
        const applyBtn = document.getElementById('blkt-palette-apply');
        if (applyBtn) {
            applyBtn.addEventListener('click', function() {
                if (hexInput) {
                    hexInput.value = tempColor;
                    hexInput.dispatchEvent(new Event('input'));
                }
                palette.style.display = 'none';
            });
        }

        // Kliknutí na přednastavené barvy
        palette.querySelectorAll('.blkt-color-preset').forEach(function(btn) {
            btn.addEventListener('click', function() {
                tempColor = this.dataset.color;
                const htmlPicker = document.getElementById('blkt-html-color-picker');
                if (htmlPicker) {
                    htmlPicker.value = tempColor;
                }
                blkt_aktualizuj_nahled_palety(tempColor);
            });
        });

        // HTML color picker
        const htmlPicker = document.getElementById('blkt-html-color-picker');
        if (htmlPicker) {
            htmlPicker.addEventListener('input', function() {
                tempColor = this.value;
                blkt_aktualizuj_nahled_palety(tempColor);
            });
        }
    }

    // Funkce pro aktualizaci náhledu v paletě
    function blkt_aktualizuj_nahled_palety(color) {
        const previewBox = document.getElementById('blkt-color-preview-box');
        if (previewBox && /^#[0-9A-Fa-f]{6}$/.test(color)) {
            previewBox.style.backgroundColor = color;

            // Kontrastní barva pro text
            const hex = color.replace('#', '');
            const r = parseInt(hex.substr(0, 2), 16);
            const g = parseInt(hex.substr(2, 2), 16);
            const b = parseInt(hex.substr(4, 2), 16);
            const brightness = ((r * 299) + (g * 587) + (b * 114)) / 1000;
            previewBox.style.color = brightness > 155 ? '#000' : '#fff';
        }

        // Označit aktivní preset
        document.querySelectorAll('.blkt-color-preset').forEach(btn => {
            if (btn.dataset.color === color) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    console.log('=== initNastaveniSection END ===');
}

// Exportujeme funkci globálně
window.initNastaveniSection = initNastaveniSection;

console.log('nastaveni.js - funkce definovány');