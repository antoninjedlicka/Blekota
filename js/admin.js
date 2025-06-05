// js/admin.js – administrační sekce s AJAX + Tabs, bez reference na section-title a footer tlačítka
document.addEventListener('DOMContentLoaded', () => {
  // ============================================
  // 1) Galerie obrázků pro TinyMCE (beze změn)
  // ============================================
  window.blkt_openGalleryModal = function(editor) {
    const overlay   = document.getElementById('blkt-gallery-overlay');
    const modal     = document.getElementById('blkt-gallery-modal');
    const galleryEl = modal.querySelector('.blkt-gallery-images');
    const btnInsert = modal.querySelector('#blkt-gallery-insert');
    let selectedUrl = '', selectedAlt = '';

    overlay.style.display = 'block';
    modal.style.display   = 'block';

    galleryEl.innerHTML = '';
    fetch('action/list_images.php')
      .then(r => r.json())
      .then(list => {
        list.forEach(img => {
          const thumb = document.createElement('img');
          thumb.src       = img.url;
          thumb.alt       = img.alt;
          thumb.title     = img.title;
          thumb.className = 'blkt-gallery-thumb';
          thumb.style.cssText = 'width:100px;height:100px;object-fit:cover;cursor:pointer;margin:.25rem;';
          thumb.addEventListener('click', () => {
            galleryEl.querySelectorAll('.selected').forEach(e => e.classList.remove('selected'));
            thumb.classList.add('selected');
            selectedUrl = img.url;
            selectedAlt = img.alt;
            btnInsert.disabled = false;
          });
          galleryEl.append(thumb);
        });
      })
      .catch(() => alert('Nepodařilo se načíst galerii.'));

    modal.querySelector('.blkt-modal-close').onclick =
    modal.querySelector('#blkt-gallery-cancel').onclick = () => {
      overlay.style.display = 'none';
      modal.style.display   = 'none';
    };

    btnInsert.onclick = () => {
      const align = modal.querySelector('#blkt-gallery-align').value;
      const disp  = modal.querySelector('#blkt-gallery-display').value;
      let style = '';
      if (align==='left' || align==='right') style = `float:${align};margin:0 1em 1em 0;`;
      else if (align==='center')              style = 'display:block;margin:0 auto 1em;';
      editor.insertContent(
        `<img src="${selectedUrl}" alt="${selectedAlt}" style="${style}display:${disp};">`
      );
      overlay.style.display = 'none';
      modal.style.display   = 'none';
    };
  };

  // ============================================
  // 2) Vložit / upravit obrázek pro TinyMCE (beze změn)
  // ============================================
  window.blkt_openImageModal = function(editor, existingImg) {
    const overlay = document.getElementById('blkt-gallery-overlay');
    const modal   = document.getElementById('blkt-gallery-modal');

    overlay.style.display = 'block';
    modal.style.display   = 'block';

    modal.querySelector('h3').textContent = existingImg ? 'Upravit obrázek' : 'Vložit obrázek';
    modal.querySelector('.blkt-modal-body').innerHTML = `
      <div class="blkt-formular-skupina">
        <input type="text" id="blkt-img-src" placeholder=" " required>
        <label for="blkt-img-src">URL obrázku</label>
      </div>
      <div class="blkt-formular-skupina">
        <input type="text" id="blkt-img-alt" placeholder=" ">
        <label for="blkt-img-alt">Alt text</label>
      </div>
      <div class="blkt-formular-skupina">
        <select id="blkt-img-align" required>
          <option value="" disabled selected></option>
          <option value="">žádné</option>
          <option value="left">vlevo (text obtéká)</option>
          <option value="right">vpravo (text obtéká)</option>
          <option value="center">na střed</option>
        </select>
        <label for="blkt-img-align">Zarovnání</label>
      </div>
      <div class="blkt-formular-skupina">
        <select id="blkt-img-display" required>
          <option value="" disabled selected></option>
          <option value="inline">vložit do textu</option>
          <option value="block">blok (nový řádek)</option>
        </select>
        <label for="blkt-img-display">Zobrazení</label>
      </div>
      <div class="modal-actions" style="text-align:right;">
        <button type="button" id="blkt-img-cancel" class="btn btn-cancel">Zrušit</button>
        <button type="button" id="blkt-img-insert" class="btn btn-save" disabled>Vložit/Uložit</button>
      </div>
    `;

    const srcIn   = document.getElementById('blkt-img-src');
    const altIn   = document.getElementById('blkt-img-alt');
    const alignIn = document.getElementById('blkt-img-align');
    const dispIn  = document.getElementById('blkt-img-display');
    const btnIns  = document.getElementById('blkt-img-insert');

    if (existingImg) {
      srcIn.value   = existingImg.src;
      altIn.value   = existingImg.alt || '';
      const s = existingImg.style;
      if (s.float==='left')       alignIn.value='left';
      else if (s.float==='right') alignIn.value='right';
      else if (s.display==='block' && s.marginLeft==='auto' && s.marginRight==='auto')
        alignIn.value='center';
      dispIn.value = s.display||'';
      btnIns.disabled = false;
    }

    modal.querySelector('#blkt-img-cancel').onclick =
    modal.querySelector('.blkt-modal-close').onclick = () => {
      overlay.style.display = 'none';
      modal.style.display   = 'none';
    };

    btnIns.onclick = () => {
      const src   = srcIn.value.trim();
      const alt   = altIn.value.trim();
      const align = alignIn.value;
      const disp  = dispIn.value;
      let style = '';
      if (align==='left' || align==='right') style = `float:${align};margin:0 1em 1em 0;`;
      else if (align==='center')              style = 'display:block;margin:0 auto 1em;';
      style += `display:${disp};`;
      const html = `<img src="${src}" alt="${alt}" style="${style}">`;

      if (existingImg) existingImg.outerHTML = html;
      else              editor.insertContent(html);

      overlay.style.display = 'none';
      modal.style.display   = 'none';
    };
  };

  // ============================================
  // 3) Navigace menu + AJAX načítání + Tabs
  // ============================================
  const menuItems    = document.querySelectorAll('.menu-item');
  const tabsNav      = document.querySelector('.blkt-tabs');
  const adminSection = document.getElementById('admin-section');
  let currentSection = 'dashboard';

  function capitalize(s) {
    return s.charAt(0).toUpperCase() + s.slice(1);
  }

  // Vykreslí horní záložky podle sekce
  function renderTabs(section) {
    if (!tabsNav) return;
    tabsNav.innerHTML = '';
    if (section === 'prispevky') {
      const o = document.createElement('button');
      o.textContent = 'Přehled';
      o.classList.add('active');
      const e = document.createElement('button');
      e.textContent = 'Editor';
      tabsNav.append(o, e);
      o.onclick = () => { o.classList.add('active'); e.classList.remove('active'); loadPrispevkyOverview(); };
      e.onclick = () => { e.classList.add('active'); o.classList.remove('active'); loadPrispevkyEditor(); };
    } else {
      const t = document.createElement('button');
      t.textContent = capitalize(section);
      t.classList.add('active');
      t.onclick = () => loadSection(section);
      tabsNav.append(t);
    }
  }

  // Načte obecnou sekci (dashboard, uživatelé, média)
  function loadSection(section) {
    currentSection = section;
    renderTabs(section);
    if (adminSection) adminSection.innerHTML = '<p>Načítám blkt-obsah-stranky…</p>';
    fetch(`content/${section}.php`)
      .then(r => { if (!r.ok) throw new Error(r.status); return r.text(); })
      .then(html => {
        if (adminSection) adminSection.innerHTML = html;
        if      (section === 'uzivatele') initUsersSection();
        else if (section === 'obrazky')   initImagesSection();
      })
      .catch(e => {
        if (adminSection) adminSection.innerHTML = `<p class="error-message">${e.message}</p>`;
      });
  }

  // Načte Přehled příspěvků
  function loadPrispevkyOverview() {
    currentSection = 'prispevky';
    renderTabs('prispevky');
    if (adminSection) adminSection.innerHTML = '<p>Načítám přehled…</p>';
    fetch('content/prispevky.php')
      .then(r => { if (!r.ok) throw new Error(r.status); return r.text(); })
      .then(html => {
        if (adminSection) adminSection.innerHTML = html;
        initPostsSection();
      })
      .catch(e => {
        if (adminSection) adminSection.innerHTML = `<p class="error-message">${e.message}</p>`;
      });
  }

  // Načte Editor příspěvků
  function loadPrispevkyEditor(data = {}) {
    currentSection = 'prispevky';
    renderTabs('prispevky');
    if (adminSection) adminSection.innerHTML = '<p>Načítám editor…</p>';
    fetch('content/editor_prispevky.php', {
      method: 'POST',
      body: new URLSearchParams(data)
    })
      .then(r => { if (!r.ok) throw new Error(r.status); return r.text(); })
      .then(html => {
        if (adminSection) adminSection.innerHTML = html;
        initEditorPrispevku(data);
      })
      .catch(e => {
        if (adminSection) adminSection.innerHTML = `<p class="error-message">${e.message}</p>`;
      });
  }

  // Levé menu – klik načte sekci
  menuItems.forEach(item => {
    item.addEventListener('click', () => {
      menuItems.forEach(i => i.classList.remove('active'));
      item.classList.add('active');
      loadSection(item.dataset.section);
    });
  });

  // Načteme výchozí Dashboard
  loadSection(currentSection);

  // ============================================
  // 4) Sekce UŽIVATELE
  // ============================================
  function initUsersSection() {
    const table   = document.getElementById('users-table');
    const card    = document.getElementById('user-card');
    const toolbar = document.getElementById('user-toolbar');
    const addBtn  = document.getElementById('add-user-btn');
    const editBtn = document.getElementById('edit-user-btn');
    const delBtn  = document.getElementById('delete-user-btn');
    const overlay = document.getElementById('blkt-user-overlay');
    const modal   = document.getElementById('blkt-user-modal');
    const form    = document.getElementById('blkt-user-form');
    const titleEl = document.getElementById('blkt-modal-title');
    const closeEl = document.getElementById('blkt-modal-close');

    table.addEventListener('click', e => {
      const row = e.target.closest('tr');
      if (!row) return;
      document.getElementById('card-id').value         = row.dataset.id;
      document.getElementById('card-jmeno').textContent    = row.dataset.jmeno;
      document.getElementById('card-prijmeni').textContent = row.dataset.prijmeni;
      document.getElementById('card-mail').textContent     = row.dataset.mail;
      document.getElementById('card-admin-text').textContent =
        row.dataset.admin==='1'?'Ano':'Ne';
      card.style.display    = 'flex';
      toolbar.style.display = 'flex';
    });

    function showModal(mode,data={}) {
      overlay.style.display = 'block';
      modal.style.display   = 'block';
      titleEl.textContent   = mode==='add'?'Přidat uživatele'
                          : mode==='edit'?'Upravit uživatele'
                                         :'Potvrďte smazání';
      let html='';
      if(mode==='delete'){
        html=`
          <input type="hidden" name="blkt_id" value="${data.id}">
          <p>Smazat <strong>${data.jmeno} ${data.prijmeni}</strong>?</p>
          <div class="modal-actions">
            <button type="button" class="btn btn-cancel" id="blkt-cancel">Ne</button>
            <button type="submit" class="btn btn-save">Ano</button>
          </div>`;
      } else {
        html=`
          <input type="hidden" name="blkt_id" value="${data.id || ''}">
          <div class="blkt-formular-skupina"><input type="text" name="blkt_jmeno" placeholder=" " required value="${data.jmeno || ''}"><label>Jméno</label></div>
          <div class="blkt-formular-skupina"><input type="text" name="blkt_prijmeni" placeholder=" " required value="${data.prijmeni || ''}"><label>Příjmení</label></div>
          <div class="blkt-formular-skupina"><input type="email" name="blkt_mail" placeholder=" " required value="${data.mail || ''}"><label>E-mail</label></div>
          <div class="blkt-formular-skupina"><select name="blkt_admin" required>
            <option value="0"${data.admin === '0' ? ' selected' : ''}>Ne</option>
            <option value="1"${data.admin === '1' ? ' selected' : ''}>Ano</option>
          </select><label>Admin</label></div>
          <div class="modal-actions">
            <button type="button" class="btn btn-cancel" id="blkt-cancel">Zrušit</button>
            <button type="submit" class="btn btn-save">${mode === 'add' ? 'Přidat' : 'Uložit'}</button>
          </div>`;
      }
      form.action = `action/${mode}_user.php`;
      form.innerHTML = html;
      document.getElementById('blkt-cancel').onclick = closeModal;
      closeEl.onclick = closeModal;
    }
    function closeModal(){ overlay.style.display='none'; modal.style.display='none'; }

    addBtn.onclick  =()=>showModal('add');
    editBtn.onclick=()=>{
      const id=document.getElementById('card-id').value;
      showModal('edit',{ id,
        jmeno:document.getElementById('card-jmeno').textContent,
        prijmeni:document.getElementById('card-prijmeni').textContent,
        mail:document.getElementById('card-mail').textContent,
        admin:document.getElementById('card-admin-text').textContent==='Ano'?'1':'0'
      });
    };
    delBtn.onclick=()=>{
      const id=document.getElementById('card-id').value;
      showModal('delete',{ id,
        jmeno:document.getElementById('card-jmeno').textContent,
        prijmeni:document.getElementById('card-prijmeni').textContent
      });
    };
    form.onsubmit=e=>{
      e.preventDefault();
      fetch(form.action,{method:'POST',body:new FormData(form)})
      .then(r=>r.json()).then(j=>{
        if(j.status==='ok'){ alert('OK'); closeModal(); loadSection('uzivatele'); }
        else alert('Chyba: '+j.error);
      }).catch(e=>alert('Síťová chyba: '+e.message));
    };
  }

// ============================================
  // 5) Sekce PŘÍSPĚVKY – Přehled
  // ============================================
  function initPostsSection() {
    const table  = document.getElementById('posts-table');
    const addBtn = document.getElementById('add-post-btn');
    if (table) {
      table.onclick = e => {
        const row = e.target.closest('tr');
        if (!row) return;
        loadPrispevkyEditor({
          id:        row.dataset.id,
          nazev:     row.dataset.nazev,
          kategorie: row.dataset.kategorie,
          obsah:     row.dataset.obsah
        });
      };
    }
    if (addBtn) addBtn.onclick = () => loadPrispevkyEditor();
  }

  // ============================================
  // 6) Sekce PŘÍSPĚVKY – inline Editor
  // ============================================
  function initEditorPrispevku(data = {}) {
    const titleEl = document.getElementById('blkt-post-title');
    const catEl   = document.getElementById('blkt-post-category');
    if (titleEl) titleEl.value = data.nazev || '';
    if (catEl)   catEl.value   = data.kategorie || '';

    tinymce.init({
      selector: '#blkt-editor',
      height:   '60vh',
      menubar:  false,
      branding: false,
      plugins: [
        'advlist autolink lists link charmap preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table wordcount emoticons'
      ],
      toolbar: [
        'undo redo | blocks fontsizeinput',
        'bold italic underline strikethrough | forecolor backcolor removeformat',
        'alignleft aligncenter alignright alignjustify alignnone | indent outdent',
        'bullist numlist | blockquote | link gallery customImage',
        'subscript superscript | code preview'
      ].join(' | '),
      setup: ed => {
        ed.on('init', () => { if (data.obsah) ed.setContent(data.obsah); });
        ed.ui.registry.addButton('gallery',     { tooltip:'Galerie', icon:'browse', onAction:()=>blkt_openGalleryModal(ed) });
        ed.ui.registry.addButton('customImage', { tooltip:'Vložit obrázek', icon:'image', onAction:()=>blkt_openImageModal(ed) });
        ed.on('DblClick', e => { if (e.target.nodeName==='IMG') blkt_openImageModal(ed, e.target); });
      }
    });

    const cancelBtn = document.getElementById('blkt-post-cancel');
    const saveBtn   = document.getElementById('blkt-post-save');
    if (cancelBtn) cancelBtn.onclick = loadPrispevkyOverview;
    if (saveBtn)   saveBtn.onclick   = () => {
      const p = new FormData();
      p.append('blkt_id',        data.id || '');
      p.append('blkt_nazev',     document.getElementById('blkt-post-title').value);
      p.append('blkt_kategorie', document.getElementById('blkt-post-category').value);
      p.append('blkt_obsah',     tinymce.get('blkt-editor').getContent());
      fetch(`action/${data.id?'edit_prispevek':'add_prispevek'}.php`, { method:'POST', body:p })
        .then(r => r.json())
        .then(j => {
          if (j.status==='ok') {
            alert('Uloženo');
            loadPrispevkyOverview();
          } else {
            alert('Chyba: '+j.error);
          }
        })
        .catch(e => alert('Síťová chyba: '+e.message));
    };
  }

  // ============================================
  // 6) Sekce OBRÁZKY
  // ============================================
  function initImagesSection() {
    const overlay   = document.getElementById('blkt-image-overlay');
    const modal     = document.getElementById('blkt-image-modal');
    const addBtn    = document.getElementById('blkt-add-image-btn');
    const closeBtn  = document.getElementById('blkt-modal-close');
    const cancelBtn = document.getElementById('blkt-image-cancel');
    const zone      = document.getElementById('blkt-upload-zone');
    const fileIn    = document.getElementById('blkt-file-input');
    const preview   = document.getElementById('blkt-preview');
    const origName  = document.getElementById('blkt-original-name');
    const form      = document.getElementById('blkt-image-form');

    // živé vyhledávání
    const searchInput=document.getElementById('blkt-search');
    if(searchInput){
      searchInput.addEventListener('input',()=>{
        const q=searchInput.value.trim().toLowerCase();
        document.querySelectorAll('.blkt-image-gallery').forEach(gallery=>{
          let any=false;
          gallery.querySelectorAll('.blkt-image-card').forEach(card=>{
            const t=(card.dataset.title||'').toLowerCase();
            const a=(card.dataset.alt||'').toLowerCase();
            const d=(card.dataset.desc||'').toLowerCase();
            const o=(card.dataset.orig||'').toLowerCase();
            const m=t.includes(q)||a.includes(q)||d.includes(q)||o.includes(q);
            card.style.display=m?'inline-block':'none';
            if(m) any=true;
          });
          gallery.style.display=any?'':'none';
          const div=gallery.previousElementSibling;
          if(div&&div.classList.contains('month-divider')) div.style.display=any?'flex':'none';
        });
      });
    }

    // přidat obrázek
    addBtn.addEventListener('click',()=>{
      form.reset(); form.action='action/add_image.php';
      preview.src=''; preview.style.display='none'; zone.style.display='flex';
      document.getElementById('blkt-modal-title').textContent='Přidat obrázek';
      overlay.style.display='block'; modal.style.display='block';
    });
    // zavření
    [overlay,closeBtn,cancelBtn].forEach(el=>el.addEventListener('click',()=>{
      overlay.style.display='none'; modal.style.display='none';
    }));
    // upload zóna
    zone.addEventListener('click',()=>fileIn.click());
    ['dragenter','dragover'].forEach(evt=>zone.addEventListener(evt,e=>{
      e.preventDefault();zone.classList.add('blkt-upload-over');
    }));
    ['dragleave','drop'].forEach(evt=>zone.addEventListener(evt,e=>{
      e.preventDefault();zone.classList.remove('blkt-upload-over');
    }));
    zone.addEventListener('drop',e=>handleFile(e.dataTransfer.files[0]));
    fileIn.addEventListener('change',()=>handleFile(fileIn.files[0]));
    function handleFile(file){
      if(!file)return;
      const reader=new FileReader();
      reader.onload=e=>{
        preview.src=e.target.result; preview.style.display='block';
        zone.style.display='none'; origName.value=file.name;
      };
      reader.readAsDataURL(file);
    }
    // odeslání
    form.addEventListener('submit',e=>{
      e.preventDefault();
      fetch(form.action,{method:'POST',body:new FormData(form)})
        .then(r=>r.json()).then(j=>{
          if(j.status==='ok'){
            alert('Obrázek uložen.');
            overlay.style.display='none'; modal.style.display='none';
            loadSection('obrazky');
          } else alert('Chyba:'+j.error);
        }).catch(e=>alert('Síťová chyba:'+e.message));
    });
    // edit/delete
    document.querySelectorAll('.blkt-image-card button').forEach(btn=>{
      btn.addEventListener('click',()=>{
        const card=btn.closest('.blkt-image-card');
        const id=card.dataset.id;
        if(btn.dataset.action==='edit'){
          form.reset(); form.action='action/edit_image.php';
          document.getElementById('blkt-modal-title').textContent='Upravit obrázek';
          form.querySelector('input[name="blkt_id"]').value=id;
          const imgElem=card.querySelector('img');
          if(imgElem){
            preview.src=imgElem.src; preview.style.display='block';
            zone.style.display='none'; origName.value=card.dataset.orig||'';
          }
          overlay.style.display='block'; modal.style.display='block';
          document.getElementById('blkt-title').value=card.dataset.title||'';
          document.getElementById('blkt-alt').value=card.dataset.alt||'';
          document.getElementById('blkt-description').value=card.dataset.desc||'';
          fetch('action/edit_image.php',{method:'POST',body:new URLSearchParams({blkt_id:id,action:'get'})})
            .then(res=>res.json()).then(j=>{
              document.getElementById('blkt-title').value=j.data.blkt_title||'';
              document.getElementById('blkt-alt').value=j.data.blkt_alt||'';
              document.getElementById('blkt-description').value=j.data.blkt_description||'';
              origName.value=j.data.blkt_original_name||origName.value;
            }).catch(()=>{});
        } else if(btn.dataset.action==='delete'){
          if(!confirm('Opravdu chcete smazat tento obrázek?')) return;
          fetch('action/delete_image.php',{method:'POST',body:new URLSearchParams({blkt_id:id})})
            .then(r=>r.json()).then(j=>{
              if(j.status==='ok') loadSection('obrazky');
              else alert('Chyba:'+j.error);
            }).catch(e=>alert('Síťová chyba:'+e.message));
        }
      });
    });
  }

}); // konec DOMContentLoaded
