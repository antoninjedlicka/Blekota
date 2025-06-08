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
    if (typeof tinymce === 'undefined') {
      console.log('TinyMCE not loaded yet, waiting...');
      setTimeout(initTinyMCE, 100);
      return;
    }

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
              thumb.className = 'blkt-gallery-thumb-modal';

              thumb.addEventListener('click', () => {
                // Označení vybrané fotky
                gallery.querySelectorAll('.blkt-gallery-thumb-modal').forEach(t => {
                  t.classList.remove('blkt-vybrano');
                });
                thumb.classList.add('blkt-vybrano');

                // Nastavení vybrané fotky
                const preview = document.getElementById('blkt-cv-foto-preview');
                preview.innerHTML = `
                                <img src="${img.url}" alt="Profilová fotografie" style="max-width: 200px; border-radius: 8px;">
                                <input type="hidden" name="cv_foto" value="${img.url}">
                            `;

                // Zavření modalu po výběru
                setTimeout(() => {
                  overlay.style.display = 'none';
                  modal.style.display = 'none';
                }, 300);
              });

              gallery.appendChild(thumb);
            });
          })
          .catch((err) => {
            console.error('Chyba při načítání galerie:', err);
            blkt_notifikace('Chyba při načítání galerie.', 'error');
          });

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

      // Informace o přidání
      blkt_notifikace(`Nová položka přidána`, 'info');
    });
  }

  // Odebírání položek
  document.addEventListener('click', e => {
    if (e.target.classList.contains('blkt-odebrat-radek') ||
        e.target.classList.contains('blkt-odebrat-pozici')) {
      if (confirm('Opravdu chcete tuto položku odebrat?')) {
        const item = e.target.closest('[data-index]');
        if (item) {
          item.remove();
          blkt_notifikace('Položka odebrána', 'info');
        }
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

      // Zobrazíme loader nebo disabled tlačítko
      const saveBtn = document.querySelector('.blkt-sticky-save button');
      if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.textContent = 'Ukládám...';
      }

      fetch(form.action, {
        method: 'POST',
        body: formData
      })
          .then(r => r.json())
          .then(response => {
            if (response.status === 'ok') {
              blkt_notifikace('Životopis byl úspěšně uložen.', 'success');
            } else {
              blkt_notifikace('Chyba při ukládání: ' + response.error, 'error');
            }
          })
          .catch(err => {
            console.error('Síťová chyba:', err);
            blkt_notifikace('Síťová chyba: ' + err.message, 'error');
          })
          .finally(() => {
            // Obnovíme tlačítko
            if (saveBtn) {
              saveBtn.disabled = false;
              saveBtn.textContent = 'Uložit všechny změny';
            }
          });
    });
  }

  // Nápověda pro emoji
  const emojiHints = document.querySelectorAll('input[name*="ikona"]');
  emojiHints.forEach(input => {
    input.addEventListener('focus', () => {
      const isWindows = navigator.platform.indexOf('Win') > -1;
      const isMac = navigator.platform.indexOf('Mac') > -1;

      let hint = '';
      if (isWindows) hint = 'Tip: Win + . (tečka) pro emoji';
      else if (isMac) hint = 'Tip: Cmd + Control + Space pro emoji';

      if (hint && !input.dataset.hintShown) {
        input.dataset.hintShown = 'true';
        blkt_notifikace(hint, 'info');
      }
    });
  });
}

// Zajistíme, že funkce blkt_notifikace existuje
if (typeof window.blkt_notifikace === 'undefined') {
  window.blkt_notifikace = function(zprava, typ = 'info') {
    console.log(`[NOTIFIKACE ${typ}] ${zprava}`);
  };
}

// Spustit po načtení
initZivotopisSection();