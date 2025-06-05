// admin/js/prispevky.js
// Detailní logování a spouštění i po dynamickém načtení

console.log('prispevky.js load start, document.readyState =', document.readyState);

// Pomocná funkce pro generování slug z názvu
function blkt_convertToSlug(text) {
  return text
    .toString()
    .normalize('NFD')                   // rozkládá diakritiku
    .replace(/\p{Diacritic}/gu, '')    // odstraní diakritiku
    .toLowerCase()
    .trim()                             // odstraní mezery na okrajích
    .replace(/[^a-z0-9 -]/g, '')        // odstranit nepovol. znaky
    .replace(/\s+/g, '-')              // mezery na pomlčky
    .replace(/-+/g, '-');               // vícenásobné pomlčky na jednu
}

// Globální modály pro obrázky
window.blkt_openGalleryModal = function(editor) {
  console.log('[GalleryModal] Opening');
  const overlay   = document.getElementById('blkt-gallery-overlay');
  const modal     = document.getElementById('blkt-gallery-modal');
  const galleryEl = modal.querySelector('.blkt-gallery-images');
  const btnInsert = document.getElementById('blkt-gallery-insert');
  let selectedUrl = '', selectedAlt = '';
  overlay.style.display = modal.style.display = 'block';
  btnInsert.disabled = true;
  galleryEl.innerHTML = '<p>Načítám…</p>';
  fetch('action/list_images.php')
    .then(r => r.json())
    .then(list => {
      console.log('[GalleryModal] Loaded', list.length, 'images');
      galleryEl.innerHTML = '';
      list.forEach(img => {
        const thumb = document.createElement('img');
        thumb.src = img.url; thumb.alt = img.alt; thumb.title = img.title;
        thumb.className = 'blkt-gallery-thumb';
        thumb.addEventListener('click', () => {
          galleryEl.querySelectorAll('.selected').forEach(e => e.classList.remove('selected'));
          thumb.classList.add('selected');
          selectedUrl = img.url; selectedAlt = img.alt;
          btnInsert.disabled = false;
          console.log('[GalleryModal] Selected', selectedUrl);
        });
        galleryEl.append(thumb);
      });
    })
    .catch(() => {
      console.error('[GalleryModal] Error loading images');
      alert('Nepodařilo se načíst galerii.');
    });
  // Zavření
  modal.querySelector('.blkt-modal-close').onclick =
  document.getElementById('blkt-gallery-cancel').onclick = () => {
    console.log('[GalleryModal] Closing');
    overlay.style.display = modal.style.display = 'none';
  };
  // Vložit
  btnInsert.onclick = () => {
    console.log('[GalleryModal] Inserting', selectedUrl);
    const align = document.getElementById('blkt-gallery-align').value;
    const disp  = document.getElementById('blkt-gallery-display').value;
    let style = '';
    if (align==='left'||align==='right') style = `float:${align};margin:0 1em 1em 0;`;
    else if (align==='center')           style = 'display:block;margin:0 auto 1em;';
    editor.insertContent(`<img src="${selectedUrl}" alt="${selectedAlt}" style="${style}display:${disp};">`);
    overlay.style.display = modal.style.display = 'none';
  };
};

window.blkt_openImageModal = function(editor, existingImg) {
  console.log('[ImageModal] Dispatching to GalleryModal');
  window.blkt_openGalleryModal(editor);
};

// Hlavní init funkce
function initPrispevky() {
  console.log('initPrispevky() start');

  const btnPrehled = document.querySelector('.blkt-tabs button[data-tab="prehled"]');
  const btnEditor  = document.querySelector('.blkt-tabs button[data-tab="editor"]');
  const cntPrehled = document.getElementById('tab-prehled');
  const cntEditor  = document.getElementById('tab-editor');
  let currentData  = {};

  function showPrehled() {
    console.log('showPrehled()');
    btnPrehled.classList.add('active');
    btnEditor.classList.remove('active');
    cntPrehled.style.display = '';
    cntEditor.style.display  = 'none';
  }

  function showEditor(data={}) {
    console.log('showEditor()', data);
    currentData = data;
    btnEditor.classList.add('active');
    btnPrehled.classList.remove('active');
    cntPrehled.style.display = 'none';
    cntEditor.style.display  = '';
    // naplnění polí
    const titleEl = document.getElementById('blkt-post-title');
    const catEl   = document.getElementById('blkt-post-category');
    const slugEl  = document.getElementById('blkt-post-slug');
    const tagsEl  = document.getElementById('blkt-post-tags');

    titleEl.value    = data.nazev    || '';
    catEl.value      = data.kategorie || '';
    slugEl.value     = data.slug     || '';
    tagsEl.value     = data.tags     || '';

    // pokud nový příspěvek, generujeme slug z názvu
    if (!data.id) {
      titleEl.addEventListener('input', e => {
        slugEl.value = blkt_convertToSlug(e.target.value);
      });
    }

    initEditorPrispevku(data);
  }

  function initPostsSection() {
    console.log('initPostsSection()');
    const table  = document.getElementById('posts-table');
    const addBtn = document.getElementById('add-post-btn');

    if (table) {
      table.addEventListener('click', e => {
        const row = e.target.closest('tr');
        if (!row) return;
        const d = {
          id:        row.dataset.id,
          nazev:     row.dataset.nazev,
          kategorie: row.dataset.kategorie,
          obsah:     row.dataset.obsah,
          slug:      row.dataset.slug,
          tags:      row.dataset.tags
        };
        console.log('table row click', d);
        showEditor(d);
      });
    }
    if (addBtn) {
      addBtn.addEventListener('click', () => {
        console.log('add-post-btn click');
        showEditor({});
      });
    }

    showPrehled();
  }

  function initEditorPrispevku(data={}) {
    console.log('initEditorPrispevku()', data);
    tinymce.remove('#blkt-editor');
    tinymce.init({
      selector:      '#blkt-editor',
      height:        '100%',
      menubar:       false,
      branding:      false,
      license_key:   'gpl',
      plugins: [
        'advlist','autolink','lists','link','charmap','preview',
        'anchor','searchreplace','visualblocks','code','fullscreen',
        'insertdatetime','media','table','wordcount','emoticons'
      ],
      toolbar: [
        'undo redo | blocks fontsizeinput',
        'bold italic underline strikethrough | forecolor backcolor removeformat',
        'alignleft aligncenter alignright alignjustify alignnone | indent outdent',
        'bullist numlist | blockquote | link gallery customImage',
        'subscript superscript | code preview'
      ].join(' | '),
      toolbar_mode: 'sliding',
      setup: ed => {
        ed.on('init', () => {
          console.log('TinyMCE init');
          if (data.obsah) {
            ed.setContent(data.obsah);
            console.log('Content set in editor');
          }
        });
        ed.ui.registry.addButton('gallery',     { tooltip:'Galerie', icon:'browse',      onAction:()=>blkt_openGalleryModal(ed) });
        ed.ui.registry.addButton('customImage', { tooltip:'Obrázek', icon:'image',        onAction:()=>blkt_openImageModal(ed) });
        ed.on('DblClick', e => {
          if (e.target.nodeName === 'IMG') {
            console.log('TinyMCE DblClick on IMG');
            blkt_openImageModal(ed, e.target);
          }
        });
      }
    });

    document.getElementById('blkt-post-cancel').onclick = () => {
      console.log('cancel click → showPrehled');
      showPrehled();
    };

    document.getElementById('blkt-post-save').onclick = () => {
      console.log('save click', currentData);
      const p = new FormData();
      p.append('blkt_id',        currentData.id || '');
      p.append('blkt_nazev',     document.getElementById('blkt-post-title').value);
      p.append('blkt_kategorie', document.getElementById('blkt-post-category').value);
      p.append('blkt_obsah',     tinymce.get('blkt-editor').getContent());
      p.append('blkt_slug',      document.getElementById('blkt-post-slug').value);
      p.append('blkt_tags',      document.getElementById('blkt-post-tags').value);

      fetch(`action/${currentData.id?'edit_prispevek':'add_prispevek'}.php`, {
        method: 'POST',
        body:   p
      })
      .then(r => r.json())
      .then(j => {
        console.log('save response', j);
        if (j.status === 'ok') {
          alert('Uloženo');
          refreshPrehled();
          showPrehled();
        } else {
          alert('Chyba: ' + j.error);
        }
      })
      .catch(e => {
        console.error('Save network error', e);
        alert('Síťová chyba: ' + e.message);
      });
    };
  }

  function refreshPrehled() {
    console.log('refreshPrehled()');
    fetch('content/prispevky.php')
      .then(r => r.text())
      .then(html => {
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        const newBody = tmp.querySelector('#tab-prehled table tbody');
        const oldBody = document.querySelector('#tab-prehled table tbody');
        if (newBody && oldBody) {
          oldBody.replaceWith(newBody);
          console.log('Table body refreshed');
        }
      });
  }

  // Spustíme init okamžitě nebo po DOMContentLoaded
  initPostsSection();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initPrispevky);
} else {
  initPrispevky();
}