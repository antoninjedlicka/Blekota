// admin/js/nastaveni.js
console.log('nastaveni.js - soubor načten');

function initNastaveniSection() {
    console.log('=== initNastaveniSection START ===');
    //
    // const form = document.getElementById('blkt-form-nastaveni');
    // if (!form) {
    //     console.error('CHYBA: Formulář nenalezen!');
    //     return;
    // }
    //
    // // AJAX SUBMIT
    // form.onsubmit = function(e) {
    //     e.preventDefault();
    //     console.log('Form submit - preventDefault');
    //
    //     const formData = new FormData(form);
    //
    //     // Debug
    //     for (let [key, value] of formData.entries()) {
    //         console.log(key + ': ' + value);
    //     }
    //
    //     fetch('action/save_nastaveni.php', {
    //         method: 'POST',
    //         body: formData
    //     })
    //         .then(r => {
    //             console.log('Response status:', r.status);
    //             if (!r.ok) {
    //                 throw new Error('HTTP error! status: ' + r.status);
    //             }
    //             return r.text(); // Nejdřív jako text
    //         })
    //         .then(text => {
    //             console.log('Response text:', text);
    //             try {
    //                 const data = JSON.parse(text); // Pak parsujeme JSON
    //                 console.log('Parsed data:', data);
    //
    //                 if (typeof window.blkt_notifikace === 'function') {
    //                     if (data.status === 'ok') {
    //                         window.blkt_notifikace('Nastavení bylo úspěšně uloženo.', 'success');
    //
    //                         // Aplikovat barvu
    //                         const newColor = document.getElementById('blkt-color-hex-input').value;
    //                         if (typeof window.blkt_aplikuj_barevne_schema === 'function') {
    //                             window.blkt_aplikuj_barevne_schema(newColor);
    //                         }
    //                     } else {
    //                         window.blkt_notifikace('Chyba: ' + data.error, 'error');
    //                     }
    //                 } else {
    //                     alert(data.status === 'ok' ? 'Uloženo!' : 'Chyba!');
    //                 }
    //             } catch (e) {
    //                 console.error('JSON parse error:', e);
    //                 console.error('Response was:', text);
    //                 alert('Chyba při zpracování odpovědi serveru');
    //             }
    //         })
    //         .catch(err => {
    //             console.error('Fetch error:', err);
    //             alert('Chyba při ukládání!');
    //         });
    //
    //     return false;
    // };
    //
    // // ZMĚNA BARVY V HEX INPUTU
    // const hexInput = document.getElementById('blkt-color-hex-input');
    // const colorBtn = document.getElementById('blkt-color-picker-btn');
    // const themeSelect = document.getElementById('blkt-theme-select');
    //
    // if (hexInput) {
    //     hexInput.oninput = function() {
    //         let value = this.value;
    //         if (value && !value.startsWith('#')) {
    //             value = '#' + value;
    //             this.value = value;
    //         }
    //
    //         if (colorBtn && /^#[0-9A-Fa-f]{6}$/.test(value)) {
    //             colorBtn.style.backgroundColor = value;
    //
    //             if (themeSelect) {
    //                 const hasOption = Array.from(themeSelect.options).some(opt => opt.value === value);
    //                 themeSelect.value = hasOption ? value : 'custom';
    //             }
    //
    //             // Okamžitá aplikace
    //             if (typeof window.blkt_aplikuj_barevne_schema === 'function') {
    //                 window.blkt_aplikuj_barevne_schema(value);
    //             }
    //         }
    //     };
    // }
    //
    // // ZMĚNA SELECTU
    // if (themeSelect) {
    //     themeSelect.onchange = function() {
    //         if (this.value !== 'custom' && hexInput) {
    //             hexInput.value = this.value;
    //             if (colorBtn) {
    //                 colorBtn.style.backgroundColor = this.value;
    //             }
    //
    //             // Okamžitá aplikace
    //             if (typeof window.blkt_aplikuj_barevne_schema === 'function') {
    //                 window.blkt_aplikuj_barevne_schema(this.value);
    //             }
    //         }
    //     };
    // }
    //
    // // OTEVŘENÍ PALETY
    // if (colorBtn) {
    //     colorBtn.onclick = function(e) {
    //         e.preventDefault();
    //
    //         const palette = document.getElementById('blkt-color-palette');
    //         if (palette) {
    //             palette.style.display = 'block';
    //
    //             // Nastavit aktuální barvu do palety
    //             const currentColor = hexInput.value;
    //             const htmlPicker = document.getElementById('blkt-html-color-picker');
    //             if (htmlPicker) {
    //                 htmlPicker.value = currentColor;
    //             }
    //
    //             // Aktualizovat náhled
    //             updatePalettePreview(currentColor);
    //         }
    //     };
    // }
    //
    // // Funkce pro aktualizaci náhledu v paletě
    // function updatePalettePreview(color) {
    //     const previewBox = document.getElementById('blkt-color-preview-box');
    //     if (previewBox && /^#[0-9A-Fa-f]{6}$/.test(color)) {
    //         previewBox.style.backgroundColor = color;
    //
    //         // Kontrastní barva pro text
    //         const hex = color.replace('#', '');
    //         const r = parseInt(hex.substr(0, 2), 16);
    //         const g = parseInt(hex.substr(2, 2), 16);
    //         const b = parseInt(hex.substr(4, 2), 16);
    //         const brightness = ((r * 299) + (g * 587) + (b * 114)) / 1000;
    //         previewBox.style.color = brightness > 155 ? '#000' : '#fff';
    //     }
    //
    //     // Označit aktivní preset
    //     document.querySelectorAll('.blkt-color-preset').forEach(btn => {
    //         if (btn.dataset.color === color) {
    //             btn.classList.add('active');
    //         } else {
    //             btn.classList.remove('active');
    //         }
    //     });
    // }
    //
    // // UDÁLOSTI V PALETĚ
    // const palette = document.getElementById('blkt-color-palette');
    // if (palette) {
    //     let tempColor = hexInput.value;
    //
    //     // Zavřít
    //     const closeBtn = palette.querySelector('.blkt-palette-close');
    //     if (closeBtn) {
    //         closeBtn.onclick = function() {
    //             palette.style.display = 'none';
    //         };
    //     }
    //
    //     // Cancel
    //     const cancelBtn = document.getElementById('blkt-palette-cancel');
    //     if (cancelBtn) {
    //         cancelBtn.onclick = function() {
    //             palette.style.display = 'none';
    //         };
    //     }
    //
    //     // Apply
    //     const applyBtn = document.getElementById('blkt-palette-apply');
    //     if (applyBtn) {
    //         applyBtn.onclick = function() {
    //             if (hexInput) {
    //                 hexInput.value = tempColor;
    //                 hexInput.oninput(); // Trigger oninput event
    //             }
    //             palette.style.display = 'none';
    //         };
    //     }
    //
    //     // Kliknutí na preset barvu
    //     palette.querySelectorAll('.blkt-color-preset').forEach(function(btn) {
    //         btn.onclick = function() {
    //             tempColor = this.dataset.color;
    //             const htmlPicker = document.getElementById('blkt-html-color-picker');
    //             if (htmlPicker) {
    //                 htmlPicker.value = tempColor;
    //             }
    //             updatePalettePreview(tempColor);
    //         };
    //     });
    //
    //     // HTML color picker
    //     const htmlPicker = document.getElementById('blkt-html-color-picker');
    //     if (htmlPicker) {
    //         htmlPicker.oninput = function() {
    //             tempColor = this.value;
    //             updatePalettePreview(tempColor);
    //         };
    //     }
    // }
    //
    // console.log('=== initNastaveniSection END ===');
}

window.initNastaveniSection = initNastaveniSection;