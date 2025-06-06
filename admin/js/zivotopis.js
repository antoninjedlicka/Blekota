// admin/js/zivotopis.js
function initZivotopisSection() {
    console.log('initZivotopisSection()');

    // Přepínání záložek
    const tabsNav = document.querySelector('.blkt-tabs');
    if (tabsNav) {
        const tabs = Array.from(tabsNav.querySelectorAll('button[data-tab]'));
        const contents = tabs.map(t => document.getElementById('tab-' + t.dataset.tab));

        function activateTab(tab) {
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => { if (c) c.style.display = 'none'; });
            tab.classList.add('active');
            const pane = document.getElementById('tab-' + tab.dataset.tab);
            if (pane) pane.style.display = '';

            // Pokud je to záložka s profesemi, inicializuj TinyMCE
            if (tab.dataset.tab === 'profese') {
                setTimeout(initTinyMCE, 100);
            }
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', () => activateTab(tab));
        });
    }

    // Inicializace TinyMCE pro profesní zkušenosti
    function initTinyMCE() {
        tinymce.remove('.blkt-tinymce-editor');
        tinymce.init({
            selector: '.blkt-tinymce-editor',
            height: 300,
            menubar: false,
            branding: false,
            license_key: 'gpl',
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'charmap',
                'preview', 'anchor', 'searchreplace', 'visualblocks',
                'code', 'fullscreen', 'insertdatetime', 'media', 'table'
            ],
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | removeformat | code',
            content_style: 'body { font-family: "Signika Negative", sans-serif; font-size: 14px; }'
        });
    }

    // Výběr fotografie
    const vyberFotoBtn = document.getElementById('blkt-vybrat-foto');
    if (vyberFotoBtn) {
        vyberFotoBtn.addEventListener('click', () => {
            const overlay = document.getElementById('blkt-foto-overlay');
            const modal = document.getElementById('blkt-foto-modal');
            const gallery = modal.querySelector('.blkt-gallery-images');

            overlay.style.display = 'block';
            modal.style.display = 'block';
            gallery.innerHTML = '<p>Načítám galerii...</p>';

            // Načtení obrázků
            fetch('action/list_images.php')
                .then(r => r.json())
                .then(images => {
                    gallery.innerHTML = '';
                    images.forEach(img => {
                        const thumb = document.createElement('img');
                        thumb.src = img.url;
                        thumb.alt = img.alt;
                        thumb.className = 'blkt-gallery-thumb';
                        thumb.style.cssText = 'width:100px;height:100px;object-fit:cover;cursor:pointer;margin:5px;border:2px solid transparent;';

                        thumb.addEventListener('click', () => {
                            // Nastavení vybrané fotky
                            const preview = document.getElementById('blkt-cv-foto-preview');
                            preview.innerHTML = `
                                <img src="${img.url}" alt="Profilová fotografie" style="max-width: 200px; border-radius: 8px;">
                                <input type="hidden" name="cv_foto" value="${img.url}">
                            `;

                            overlay.style.display = 'none';
                            modal.style.display = 'none';
                        });

                        gallery.appendChild(thumb);
                    });
                })
                .catch(() => alert('Chyba při načítání galerie.'));

            // Zavření modalu
            modal.querySelector('.blkt-modal-close').onclick =
                document.getElementById('blkt-foto-cancel').onclick = () => {
                    overlay.style.display = 'none';
                    modal.style.display = 'none';
                };
        });
    }

    // Přidávání nových položek
    setupDynamicItems('profese', 'blkt-profese-template', 'blkt-pridat-profesi', 'blkt-profese-container');
    setupDynamicItems('dovednosti', 'blkt-dovednost-template', 'blkt-pridat-dovednost', 'blkt-dovednosti-container');
    setupDynamicItems('vlastnosti', 'blkt-vlastnost-template', 'blkt-pridat-vlastnost', 'blkt-vlastnosti-container');
    setupDynamicItems('jazyky', 'blkt-jazyk-template', 'blkt-pridat-jazyk', 'blkt-jazyky-container');
    setupDynamicItems('vzdelani', 'blkt-vzdelani-template', 'blkt-pridat-vzdelani', 'blkt-vzdelani-container');

    function setupDynamicItems(type, templateId, addBtnId, containerId) {
        const template = document.getElementById(templateId);
        const addBtn = document.getElementById(addBtnId);
        const container = document.getElementById(containerId);

        if (!template || !addBtn || !container) return;

        addBtn.addEventListener('click', () => {
            const items = container.querySelectorAll('[data-index]');
            const newIndex = items.length;
            const html = template.innerHTML.replace(/{{index}}/g, newIndex);

            const div = document.createElement('div');
            div.innerHTML = html;
            container.appendChild(div.firstElementChild);

            // Pro profese znovu inicializovat TinyMCE
            if (type === 'profese') {
                setTimeout(initTinyMCE, 100);
            }
        });
    }

    // Odebírání položek
    document.addEventListener('click', e => {
        if (e.target.classList.contains('blkt-odebrat-radek') ||
            e.target.classList.contains('blkt-odebrat-pozici')) {
            if (confirm('Opravdu chcete tuto položku odebrat?')) {
                e.target.closest('[data-index]').remove();
            }
        }
    });

    // Uložení formuláře
    const form = document.getElementById('blkt-form-zivotopis');
    if (form) {
        form.addEventListener('submit', e => {
            e.preventDefault();

            // Pro TinyMCE musíme nejdřív uložit obsah
            if (typeof tinymce !== 'undefined') {
                tinymce.triggerSave();
            }

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
                .then(r => r.json())
                .then(response => {
                    if (response.status === 'ok') {
                        alert('Životopis byl úspěšně uložen.');
                    } else {
                        alert('Chyba při ukládání: ' + response.error);
                    }
                })
                .catch(err => {
                    alert('Síťová chyba: ' + err.message);
                });
        });
    }
}

// Spustit po načtení - TOTO JE DŮLEŽITÉ PRO admin.js
initZivotopisSection();