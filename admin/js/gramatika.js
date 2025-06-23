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
        });
    }

    // Inicializace tag inputů
    initTagInputs();

    // Formulář
    const form = document.getElementById('blkt-form-gramatika');
    if (!form) return;

    // AJAX odeslání formuláře
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    blkt_notifikace('Nastavení uloženo', 'success');
                } else {
                    blkt_notifikace('Chyba při ukládání: ' + (data.error || 'Neznámá chyba'), 'error');
                }
            })
            .catch(error => {
                blkt_notifikace('Chyba při komunikaci se serverem', 'error');
                console.error('Error:', error);
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
            const upravTisice = document.querySelector('[name="gramatika_tisice"]')?.checked || false;
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

            // 4. Formátování tisíců - MUSÍ BÝT PŘED číslovkami s jednotkami
            if (upravTisice) {
                // Najít všechna čísla s více než 3 číslicemi
                upravenyText = upravenyText.replace(/\b(\d{1,3})(\d{3})+\b/g, function(match) {
                    // Rozdělit číslo na skupiny po třech číslicích zprava
                    return match.replace(/\B(?=(\d{3})+(?!\d))/g, '<span class="nbsp">&nbsp;</span>');
                });
            }

            // 5. Číslovky a jednotky
            if (upravCislovky) {
                jednotky.forEach(jednotka => {
                    // Regex pro číslo následované mezerou a jednotkou
                    const regex = new RegExp(`(\\d+)\\s+(${jednotka})\\b`, 'gi');
                    upravenyText = upravenyText.replace(regex, '$1<span class="nbsp">&nbsp;</span>$2');
                });
            }

            // 6. České uvozovky
            if (upravUvozovky) {
                // Jednoduché nahrazení - první uvozovka otevírací, druhá zavírací
                let pocetUvozovek = 0;
                upravenyText = upravenyText.replace(/"/g, function() {
                    pocetUvozovek++;
                    return pocetUvozovek % 2 === 1 ? '„' : '"';
                });
            }

            // 7. Pomlčky mezi čísly
            if (upravPomlcky) {
                upravenyText = upravenyText.replace(/(\d+)\s*-\s*(\d+)/g, '$1–$2');
            }

            // 8. Tři tečky na elipsu
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