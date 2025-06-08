// admin/js/obrazky.js
function initObrazkySection() {
  console.log('obrazky.js loaded');

  // záložky + panely
  const tabsNav  = document.querySelector('.blkt-tabs');
  if (!tabsNav) return;

  const tabs     = Array.from(tabsNav.querySelectorAll('button[data-tab]'));
  const contents = tabs.map(t => document.getElementById('tab-' + t.dataset.tab)).filter(Boolean);
  let currentData = {};

  function activateTab(name) {
    tabs.forEach(btn => btn.classList.toggle('active', btn.dataset.tab === name));
    contents.forEach(c => c.style.display = (c.id === 'tab-' + name ? '' : 'none'));
  }

  tabs.forEach(btn => btn.addEventListener('click', () => {
    activateTab(btn.dataset.tab);
    if (btn.dataset.tab === 'editor') bindEditor();
  }));

  function refreshPrehled() {
    fetch('content/obrazky.php')
        .then(r => r.text())
        .then(html => {
          const tmp     = document.createElement('div');
          tmp.innerHTML = html;
          const newSec  = tmp.querySelector('#tab-prehled .admin-section');
          const oldSec  = document.querySelector('#tab-prehled .admin-section');
          if (newSec && oldSec) {
            oldSec.replaceWith(newSec);
            console.log('Přehled obnověn');
            bindPrehled();
          }
        })
        .catch(err => {
          console.error(err);
          blkt_notifikace('Chyba při obnovení přehledu', 'error');
        });
  }

  function bindPrehled() {
    // Přidat nový
    const addBtn = document.getElementById('blkt-add-image-btn');
    if (addBtn) addBtn.onclick = () => {
      currentData = {};
      activateTab('editor');
      bindEditor();
    };

    // Upravit
    document.querySelectorAll('.blkt-image-card button[data-action="edit"]')
        .forEach(btn => btn.onclick = () => {
          const card = btn.closest('.blkt-image-card');
          currentData = {
            id:   card.dataset.id,
            url:  card.dataset.url,
            orig: card.dataset.orig,
            title:card.dataset.title,
            alt:  card.dataset.alt,
            desc: card.dataset.desc
          };
          activateTab('editor');
          bindEditor();
        });

    // Smazat
    document.querySelectorAll('.blkt-image-card button[data-action="delete"]')
        .forEach(btn => btn.onclick = () => {
          const id = btn.closest('.blkt-image-card').dataset.id;
          if (!confirm('Opravdu chcete smazat tento obrázek?')) return;

          fetch('action/delete_image.php', {
            method:'POST',
            body:new URLSearchParams({blkt_id:id})
          })
              .then(r=>r.json())
              .then(j=>{
                if (j.status==='ok') {
                  blkt_notifikace('Obrázek byl smazán', 'success');
                  refreshPrehled();
                }
                else blkt_notifikace('Chyba: '+j.error, 'error');
              })
              .catch(err => {
                console.error(err);
                blkt_notifikace('Chyba při mazání obrázku', 'error');
              });
        });

    // Live search
    const searchIn = document.getElementById('blkt-search');
    if (searchIn) searchIn.oninput = () => {
      const q = searchIn.value.trim().toLowerCase();
      document.querySelectorAll('.blkt-image-card').forEach(card=>{
        const hay = Object.values(card.dataset).join(' ').toLowerCase();
        card.style.display = hay.includes(q) ? 'inline-block' : 'none';
      });

      // Skrýt/zobrazit měsíční rozdělení podle výsledků
      document.querySelectorAll('.month-divider').forEach(divider => {
        const nextGallery = divider.nextElementSibling;
        if (nextGallery && nextGallery.classList.contains('blkt-image-gallery')) {
          const visibleCards = nextGallery.querySelectorAll('.blkt-image-card[style*="inline-block"]');
          divider.style.display = visibleCards.length > 0 ? 'flex' : 'none';
        }
      });
    };
  }

  function bindEditor() {
    console.log('bindEditor()', currentData);
    const form      = document.getElementById('blkt-image-form');
    const fileIn    = document.getElementById('blkt-file-input');
    const preview   = document.getElementById('blkt-preview');
    const zone      = document.getElementById('blkt-upload-zone');
    const origIn    = document.getElementById('blkt-original-name');
    const titleIn   = document.getElementById('blkt-title');
    const altIn     = document.getElementById('blkt-alt');
    const descIn    = document.getElementById('blkt-description');
    const cancelBtn = document.getElementById('blkt-image-cancel');

    // Zrušit → Přehled
    if (cancelBtn) cancelBtn.onclick = () => activateTab('prehled');

    // Drag&Drop + click
    function handleFile(file) {
      if (!file) return;
      const reader = new FileReader();
      reader.onload = e => {
        preview.src           = e.target.result;
        preview.style.display = 'block';
        zone.style.display    = 'none';
        origIn.value          = file.name;
      };
      reader.readAsDataURL(file);
    }

    if (zone) {
      zone.onclick = e => { if (e.target===zone || e.target.parentElement===zone) fileIn.click(); };

      ['dragenter','dragover'].forEach(evt=>
          zone.addEventListener(evt,e=>{ e.preventDefault(); zone.classList.add('blkt-upload-over'); })
      );
      ['dragleave'].forEach(evt=>
          zone.addEventListener(evt,e=>{ e.preventDefault(); zone.classList.remove('blkt-upload-over'); })
      );
      zone.addEventListener('drop',e=>{
        e.preventDefault();
        const files = e.dataTransfer.files;
        if (files.length > 0) {
          handleFile(files[0]);
        }
        zone.classList.remove('blkt-upload-over');
      });
    }

    if (fileIn) {
      fileIn.onchange = () => {
        console.log('fileIn onchange', fileIn.files);
        if (fileIn.files.length > 0) {
          handleFile(fileIn.files[0]);
        }
      };
    }

    // Naplnit / reset
    if (currentData.id) {
      document.getElementById('blkt-image-id').value = currentData.id;
      preview.src    = currentData.url;
      preview.style.display = 'block';
      origIn.value   = currentData.orig;
      titleIn.value  = currentData.title;
      altIn.value    = currentData.alt;
      descIn.value   = currentData.desc;
      zone.style.display    = 'none';
    } else {
      form.reset();
      preview.style.display = 'none';
      zone.style.display    = 'block';
    }

    // Odeslání formu
    if (form) form.onsubmit = e => {
      e.preventDefault();
      const data = new FormData(form);
      const action = currentData.id ? 'edit_image.php' : 'add_image.php';

      // Zkontrolujme, že FormData obsahuje soubor při přidávání nového:
      if (!currentData.id) {
        const fileInput = form.querySelector('input[type="file"]');
        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
          return blkt_notifikace('Vyberte prosím soubor.', 'warning');
        }
      }

      fetch(`action/${action}`, { method:'POST', body: data })
          .then(r => r.json())
          .then(j => {
            console.log('save response', j);
            if (j.status==='ok') {
              blkt_notifikace('Obrázek byl uložen', 'success');
              activateTab('prehled');
              refreshPrehled();
            } else {
              blkt_notifikace('Chyba: ' + j.error, 'error');
            }
          })
          .catch(err => {
            console.error(err);
            blkt_notifikace('Síťová chyba', 'error');
          });
    };
  }

  // start
  activateTab('prehled');
  bindPrehled();
}

// Zajistíme, že funkce blkt_notifikace existuje
if (typeof window.blkt_notifikace === 'undefined') {
  window.blkt_notifikace = function(zprava, typ = 'info') {
    console.log(`[NOTIFIKACE ${typ}] ${zprava}`);
  };
}

// Spustíme inicializaci
initObrazkySection();