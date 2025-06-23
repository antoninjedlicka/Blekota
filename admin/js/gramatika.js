// admin/js/gramatika.js
// Funkce pro správu nastavení české gramatiky

function initGramatikaSection() {

    // Inicializace tag inputů
    function initTagInputs() {
        const tagWrappers = document.querySelectorAll('.blkt-tag-input-wrapper');

        tagWrappers.forEach(wrapper => {
            const input = wrapper.querySelector('.blkt-tag-input');
            const hiddenInput = wrapper.nextElementSibling;
            const dataName = wrapper.dataset.name;

            // Funkce pro aktualizaci hidden inputu
            function updateHiddenInput() {
                const tags = [];
                wrapper.querySelectorAll('.blkt-tag').forEach(tag => {
                    const text = tag.firstChild.textContent.trim();
                    tags.push(text);
                });
                hiddenInput.value = tags.join(',');

                // Trigger change event pro náhled
                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            }

            // Funkce pro přidání tagu
            function addTag(value) {
                value = value.trim();
                if (!value) return;

                // Zkontrolovat duplicity
                const existingTags = [];
                wrapper.querySelectorAll('.blkt-tag').forEach(tag => {
                    existingTags.push(tag.firstChild.textContent.trim().toLowerCase());
                });

                if (existingTags.includes(value.toLowerCase())) {
                    blkt_notifikace('Tato hodnota už je přidaná', 'warning');
                    return;
                }

                // Vytvořit nový tag
                const tagElement = document.createElement('span');
                tagElement.className = 'blkt-tag';
                tagElement.innerHTML = `
                    ${value}
                    <button type="button" class="blkt-tag-remove" aria-label="Odebrat">×</button>
                `;

                // Vložit před input
                wrapper.insertBefore(tagElement, input);

                // Přidat event listener pro odebrání
                tagElement.querySelector('.blkt-tag-remove').addEventListener('click', function() {
                    tagElement.remove();
                    updateHiddenInput();
                });

                // Vyčistit input a aktualizovat hidden
                input.value = '';
                updateHiddenInput();
            }

            // Event listenery pro input
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    addTag(this.value);
                }
            });

            input.addEventListener('blur', function() {
                if (this.value.trim()) {
                    addTag(this.value);
                }
            });

            // Event listenery pro existující tagy
            wrapper.querySelectorAll('.blkt-tag-remove').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.parentElement.remove();
                    updateHiddenInput();
                });
            });

            // Kliknutí na wrapper zaměří input
            wrapper.addEventListener('click', function(e) {
                if (e.target === wrapper) {
                    input.focus();
                }
            });

            // Focus/blur pro label efekt
            input.addEventListener('focus', function() {
                wrapper.classList.add('has-focus');
            });

            input.addEventListener('blur', function() {
                wrapper.classList.remove('has-focus');
            });

            // Pokud jsou nějaké tagy, nastavit label nahoru
            if (wrapper.querySelectorAll('.blkt-tag').length > 0) {
                wrapper.classList.add('has-focus');
            }
        });
    }console.log('=== initGramatikaSection START ===');

    const form = document.getElementById('blkt-form-gramatika');
    if (!form) {
        console.error('Formulář gramatiky nenalezen!');
        return;
    }

    // Inicializace tag inputů
    initTagInputs();

    // AJAX submit formuláře
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Odesílám nastavení gramatiky...');

        const formData = new FormData(form);

        // Debug - vypsat co odesíláme
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }

        fetch('action/save_gramatika.php', {
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
                            window.blkt_notifikace('Nastavení gramatiky bylo úspěšně uloženo.', 'success');

                            // Aktualizovat náhled
                            blkt_aktualizuj_nahled_gramatiky();
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

    // Živý náhled úprav
    const testInput = document.getElementById('blkt-gramatika-test-input');
    const testOutput = document.getElementById('blkt-gramatika-test-output');

    if (testInput && testOutput) {
        // Funkce pro aplikaci gramatických pravidel
        function blkt_aplikuj_gramatiku(text) {
            // Získat aktuální nastavení z formuláře - nyní z hidden inputů
            const predlozky = document.querySelector('[name="gramatika_predlozky"]').value.split(',').map(s => s.trim()).filter(s => s);
            const spojky = document.querySelector('[name="gramatika_spojky"]').value.split(',').map(s => s.trim()).filter(s => s);
            const zkratky = document.querySelector('[name="gramatika_zkratky"]').value.split(',').map(s => s.trim()).filter(s => s);
            const jednotky = document.querySelector('[name="gramatika_jednotky"]').value.split(',').map(s => s.trim()).filter(s => s);

            const upravCislovky = document.querySelector('[name="gramatika_cislovky"]').checked;
            const upravUvozovky = document.querySelector('[name="gramatika_uvozovky"]').checked;
            const upravPomlcky = document.querySelector('[name="gramatika_pomlcky"]').checked;
            const upravTecky = document.querySelector('[name="gramatika_tecky"]').checked;

            let upravenyText = text;

            // 1. Předložky - přidat nezalomitelnou mezeru za jednopísmenné předložky
            predlozky.forEach(predlozka => {
                const regex = new RegExp(`\\b${predlozka}\\s+`, 'gi');
                upravenyText = upravenyText.replace(regex, `${predlozka}<span class="nbsp">&nbsp;</span>`);
            });

            // 2. Spojky - stejně jako předložky
            spojky.forEach(spojka => {
                const regex = new RegExp(`\\b${spojka}\\s+`, 'gi');
                upravenyText = upravenyText.replace(regex, `${spojka}<span class="nbsp">&nbsp;</span>`);
            });

            // 3. Zkratky s tečkou
            zkratky.forEach(zkratka => {
                // Escapovat tečku v regexu
                const escapedZkratka = zkratka.replace(/\./g, '\\.');
                const regex = new RegExp(`\\b${escapedZkratka}\\s+`, 'gi');
                upravenyText = upravenyText.replace(regex, `${zkratka}<span class="nbsp">&nbsp;</span>`);
            });

            // 4. Číslovky a jednotky
            if (upravCislovky) {
                jednotky.forEach(jednotka => {
                    // Regex pro číslo následované mezerou a jednotkou
                    const regex = new RegExp(`(\\d+)\\s+(${jednotka})\\b`, 'gi');
                    upravenyText = upravenyText.replace(regex, '$1<span class="nbsp">&nbsp;</span>$2');
                });
            }

            // 5. České uvozovky
            if (upravUvozovky) {
                // Jednoduché nahrazení - první uvozovka otevírací, druhá zavírací
                let pocetUvozovek = 0;
                upravenyText = upravenyText.replace(/"/g, function() {
                    pocetUvozovek++;
                    return pocetUvozovek % 2 === 1 ? '„' : '"';
                });
            }

            // 6. Pomlčky mezi čísly
            if (upravPomlcky) {
                upravenyText = upravenyText.replace(/(\d+)\s*-\s*(\d+)/g, '$1–$2');
            }

            // 7. Tři tečky na elipsu
            if (upravTecky) {
                upravenyText = upravenyText.replace(/\.\.\./g, '…');
            }

            return upravenyText;
        }

        // Funkce pro aktualizaci náhledu
        function blkt_aktualizuj_nahled_gramatiky() {
            const vstupniText = testInput.value;
            const upravenyText = blkt_aplikuj_gramatiku(vstupniText);
            testOutput.innerHTML = upravenyText;
        }

        // Event listenery pro živý náhled
        testInput.addEventListener('input', blkt_aktualizuj_nahled_gramatiky);

        // Sledovat změny v nastavení
        form.addEventListener('change', blkt_aktualizuj_nahled_gramatiky);

        // První aktualizace náhledu
        blkt_aktualizuj_nahled_gramatiky();
    }

    console.log('=== initGramatikaSection END ===');
}

// Spustit inicializaci
initGramatikaSection();