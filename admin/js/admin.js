// admin/js/admin.js
// Kompletní administrační sekce s AJAX + Tabs + Notifikace + Responsivní záložky + LOADER
// Sloučená verze všech funkcí

document.addEventListener('DOMContentLoaded', () => {
  // ============================================
  // LOADER SYSTÉM - HNED NA ZAČÁTKU
  // ============================================
  const AdminLoader = {
    element: null,
    progressBar: null,
    progress: 0,
    isActive: false,

    init() {
      this.element = document.getElementById('blkt-admin-loader');
      this.progressBar = document.querySelector('.blkt-loader-progress-bar');

      if (!this.element) {
        console.warn('Admin loader element not found');
        return;
      }

      // Skryjeme loader po inicializaci (zobrazí se jen při přepínání sekcí)
      this.hide();
    },

    simulateProgress() {
      // Pomalejší průběh pro lepší viditelnost
      this.setProgress(20);

      // Postupné načítání rozložené do 1 sekundy
      setTimeout(() => this.setProgress(40), 200);
      setTimeout(() => this.setProgress(60), 400);
      setTimeout(() => this.setProgress(80), 600);
      setTimeout(() => this.setProgress(95), 800);

      // Dokončení vždy po 1 sekundě
      setTimeout(() => this.complete(), 1000);
    },

    setProgress(percent) {
      this.progress = percent;
      if (this.progressBar) {
        this.progressBar.style.width = percent + '%';
      }
    },

    complete() {
      this.setProgress(100);

      // Počkáme chvilku na 100% než skryjeme
      setTimeout(() => {
        this.hide();
      }, 200);
    },

    show() {
      if (!this.element) return;

      this.isActive = true;
      this.element.classList.remove('hidden');
      this.setProgress(0);

      // Reset animace
      setTimeout(() => {
        this.simulateProgress();
      }, 100);
    },

    hide() {
      if (!this.element || !this.isActive) return;

      this.isActive = false;
      this.element.classList.add('hidden');

      // Reset progress po skrytí
      setTimeout(() => {
        this.setProgress(0);
      }, 500);
    }
  };

  // Inicializace loaderu
  AdminLoader.init();

  // Globální funkce pro loader
  window.showAdminLoader = () => AdminLoader.show();
  window.hideAdminLoader = () => AdminLoader.hide();

  // ============================================
  // PROMĚNNÉ A INICIALIZACE
  // ============================================
  const menuItems    = document.querySelectorAll('.menu-item');
  const adminSection = document.getElementById('admin-section');
  let currentSection = 'dashboard';

  // ============================================
  // SYSTÉM NOTIFIKACÍ
  // ============================================
  window.blkt_notifikace = function(zprava, typ = 'success') {
    // Odstraň existující notifikace
    document.querySelectorAll('.blkt-notifikace').forEach(el => el.remove());

    // Vytvoř novou notifikaci
    const notif = document.createElement('div');
    notif.className = `blkt-notifikace blkt-notifikace-${typ}`;
    notif.innerHTML = `
      <div class="blkt-notifikace-obsah">
        <span class="blkt-notifikace-ikona">${typ === 'success' ? '✓' : '✕'}</span>
        <span class="blkt-notifikace-text">${zprava}</span>
      </div>
    `;

    // Přidej do admin-content
    const adminContent = document.querySelector('.admin-content');
    if (adminContent) {
      adminContent.appendChild(notif);

      // Animace zobrazení
      setTimeout(() => notif.classList.add('blkt-notifikace-zobrazena'), 10);

      // Automatické skrytí po 3 sekundách
      setTimeout(() => {
        notif.classList.remove('blkt-notifikace-zobrazena');
        setTimeout(() => notif.remove(), 300);
      }, 3000);
    }
  };

  // ============================================
  // URL ROUTING
  // ============================================

  // Získání sekce z URL
  function blkt_ziskej_sekci_z_url() {
    const hash = window.location.hash.replace('#', '');
    return hash || 'dashboard';
  }

  // Aktualizace URL bez refreshe
  function blkt_aktualizuj_url(sekce) {
    history.pushState({ sekce }, '', `#${sekce}`);
  }

  // ============================================
  // RESPONSIVNÍ ZÁLOŽKY
  // ============================================
  function initResponsiveTabs() {
    const tabsContainer = document.querySelector('.blkt-tabs');
    if (!tabsContainer) {
      console.log('initResponsiveTabs() - nenalezen .blkt-tabs');
      return;
    }

    console.log('initResponsiveTabs() - začátek');

    // Pokud už existuje wrapper, odstraníme ho (pro případ reinicializace)
    const existingWrapper = tabsContainer.querySelector('.blkt-tabs-wrapper');
    if (existingWrapper) {
      console.log('Odstraňuji existující wrapper');
      // Přesuneme záložky zpět do kontejneru
      const tabs = Array.from(existingWrapper.querySelectorAll('button[data-tab]'));
      tabs.forEach(tab => tabsContainer.appendChild(tab));
      existingWrapper.remove();
    }

    // Odstraníme existující more button a dropdown
    const existingMore = tabsContainer.querySelector('.blkt-tabs-more');
    const existingDropdown = tabsContainer.querySelector('.blkt-tabs-dropdown');
    if (existingMore) existingMore.remove();
    if (existingDropdown) existingDropdown.remove();

    // Vytvoříme wrapper a tlačítko pro více možností
    const wrapper = document.createElement('div');
    wrapper.className = 'blkt-tabs-wrapper';

    const moreBtn = document.createElement('button');
    moreBtn.className = 'blkt-tabs-more';
    moreBtn.innerHTML = '⋯'; // Tři tečky
    moreBtn.style.display = 'none';

    const dropdown = document.createElement('div');
    dropdown.className = 'blkt-tabs-dropdown';

    // Přesuneme všechny záložky do wrapperu
    const tabs = Array.from(tabsContainer.querySelectorAll('button[data-tab]'));
    console.log('Počet záložek:', tabs.length);

    tabs.forEach(tab => wrapper.appendChild(tab));

    tabsContainer.appendChild(wrapper);
    tabsContainer.appendChild(moreBtn);
    tabsContainer.appendChild(dropdown);

    // Funkce pro kontrolu, které záložky se vejdou
    function checkTabsOverflow() {
      console.log('checkTabsOverflow() - začátek');

      if (window.innerWidth <= 500) {
        // Na mobilu necháme horizontální scroll
        moreBtn.style.display = 'none';
        tabs.forEach(tab => tab.style.display = '');
        return;
      }

      // Resetujeme zobrazení všech záložek
      tabs.forEach(tab => {
        tab.style.display = '';
      });

      // Počkáme na překreslení
      requestAnimationFrame(() => {
        // OPRAVA: Získáme skutečnou šířku přímo z tabs kontejneru
        const tabsRect = tabsContainer.getBoundingClientRect();
        const containerWidth = tabsRect.width - 48; // Odečteme padding záložek (2x24px)

        console.log('Šířka tabs kontejneru:', tabsRect.width, 'Dostupná šířka:', containerWidth);

        let totalWidth = 0;
        dropdown.innerHTML = '';
        let hasHiddenTabs = false;

        // Nejdřív všechny záložky zobrazíme pro správné měření
        tabs.forEach(tab => {
          tab.style.display = '';
          tab.classList.remove('blkt-tab-hidden');
        });

        // Změříme skutečné rozměry každé záložky
        const tabSizes = tabs.map(tab => {
          const rect = tab.getBoundingClientRect();
          return rect.width;
        });

        // DŮLEŽITÁ ZMĚNA: Přidáme větší rezervu pro tlačítko more a bezpečnostní prostor
        const moreButtonReserve = 100; // Zvýšíme z 60 na 100px

        // Teď procházíme a schovávame ty, které se nevejdou
        tabs.forEach((tab, index) => {
          const tabWidth = tabSizes[index];
          totalWidth += tabWidth + 2; // +2 pro gap

          console.log(`Tab ${index}: šířka=${tabWidth}, celkem=${totalWidth}`);

          if (totalWidth > containerWidth - moreButtonReserve) { // Použijeme větší rezervu
            // Schováme záložku
            tab.style.display = 'none';
            tab.classList.add('blkt-tab-hidden');

            // Přidáme do dropdown
            const dropdownTab = tab.cloneNode(true);
            dropdownTab.style.display = 'block';
            dropdownTab.classList.remove('blkt-tab-hidden');

            // Zkopírujeme třídy včetně active
            if (tab.classList.contains('active')) {
              dropdownTab.classList.add('active');
            }

            dropdownTab.addEventListener('click', () => {
              tab.click();
              dropdown.classList.remove('active');
            });

            dropdown.appendChild(dropdownTab);
            hasHiddenTabs = true;
          }
        });

        moreBtn.style.display = hasHiddenTabs ? 'block' : 'none';
        console.log('Skryté záložky:', hasHiddenTabs);
      });
    }

    // Event listenery
    moreBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      dropdown.classList.toggle('active');
    });

    document.addEventListener('click', () => {
      dropdown.classList.remove('active');
    });

    // Spustíme kontrolu s malým zpožděním pro jistotu správného vykreslení
    setTimeout(() => {
      checkTabsOverflow();
      // Ještě jednou po delší době pro jistotu
      setTimeout(() => {
        checkTabsOverflow();
      }, 200);
    }, 50);

    // A při změně velikosti okna
    let resizeTimeout;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(checkTabsOverflow, 100);
    });

    // Uložíme funkci pro pozdější použití
    tabsContainer._checkOverflow = checkTabsOverflow;
  }

  // ============================================
  // 1) Galerie obrázků pro TinyMCE
  // ============================================
  window.blkt_openGalleryModal = function(editor) {
    const overlay   = document.getElementById('blkt-gallery-overlay');
    const modal     = document.getElementById('blkt-gallery-modal');
    const galleryEl = modal.querySelector('.blkt-gallery-images');
    const btnInsert = modal.querySelector('#blkt-gallery-insert');
    let selectedUrl = '', selectedAlt = '';

    overlay.style.display = 'block';
    modal.style.display   = 'block';

    galleryEl.innerHTML = '<p>Načítám galerii...</p>';
    fetch('action/list_images.php')
        .then(r => r.json())
        .then(list => {
          galleryEl.innerHTML = '';
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
        .catch(() => blkt_notifikace('Nepodařilo se načíst galerii.', 'error'));

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
  // 2) Vložit / upravit obrázek pro TinyMCE
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

    srcIn.addEventListener('input', () => {
      btnIns.disabled = !srcIn.value.trim();
    });

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
  // 3) Správa záložek (tabs)
  // ============================================
  function initTabs() {
    const tabsNav = document.querySelector('.blkt-tabs');
    if (!tabsNav) return;

    console.log('initTabs() - začátek');

    const tabs     = Array.from(tabsNav.querySelectorAll('button[data-tab]'));
    const contents = tabs
        .map(t => document.getElementById(`tab-${t.dataset.tab}`))
        .filter(Boolean);

    function activateTab(tab) {
      tabs.forEach(t => t.classList.remove('active'));
      contents.forEach(c => c.style.display = 'none');
      tab.classList.add('active');
      const pane = document.getElementById(`tab-${tab.dataset.tab}`);
      if (pane) pane.style.display = '';

      // Aktualizujeme i dropdown, pokud existuje
      const dropdown = tabsNav.querySelector('.blkt-tabs-dropdown');
      if (dropdown) {
        dropdown.querySelectorAll('button').forEach(btn => {
          btn.classList.toggle('active', btn.dataset.tab === tab.dataset.tab);
        });
      }
    }

    tabs.forEach(tab => {
      tab.addEventListener('click', () => activateTab(tab));
    });

    if (tabs.length) activateTab(tabs[0]);

    // Inicializujeme responsivní záložky s malým zpožděním pro správné měření
    setTimeout(() => {
      console.log('Volám initResponsiveTabs()');
      initResponsiveTabs();

      // Ještě jednou zavoláme kontrolu po delším čase pro jistotu
      setTimeout(() => {
        const tabsContainer = document.querySelector('.blkt-tabs');
        if (tabsContainer && tabsContainer._checkOverflow) {
          console.log('Dodatečná kontrola záložek');
          tabsContainer._checkOverflow();
        }
      }, 300);
    }, 100);
  }

  // ============================================
  // 4) Dynamické načtení JS pro danou sekci
  // ============================================
  function loadSectionScript(section) {
    const prev = document.getElementById('section-script');
    if (prev) prev.remove();

    // Pro některé sekce nepotřebujeme speciální JS
    const sectionsWithoutJS = ['nastaveni'];
    if (sectionsWithoutJS.includes(section)) {
      console.log(`Sekce ${section} nemá vlastní JS`);
      return;
    }

    const script = document.createElement('script');
    script.id    = 'section-script';
    script.defer = true;
    script.src   = `js/${section}.js?v=${Date.now()}`; // Cache busting

    script.onload = () => {
      console.log(`Script ${section}.js načten`);
    };
    script.onerror = () => {
      console.warn(`Soubor js/${section}.js neexistuje nebo se nepodařilo načíst`);
    };

    document.body.appendChild(script);
  }

  // ============================================
  // 5) Načtení a vykreslení sekce S LOADEREM
  // ============================================
  function loadSection(section, updateUrl = true) {
    currentSection = section;

    // Zobrazíme loader
    AdminLoader.show();

    // Aktualizuj URL pouze pokud je to žádoucí
    if (updateUrl) {
      blkt_aktualizuj_url(section);
    }

    // Aktualizuj aktivní položku menu
    menuItems.forEach(item => {
      item.classList.toggle('active', item.dataset.section === section);
    });

    // Nejdřív skryjeme obsah
    adminSection.style.opacity = '0';

    // Minimální doba zobrazení loaderu (1 sekunda)
    const minLoaderTime = 1000;
    const startTime = Date.now();

    fetch(`content/${section}.php`)
        .then(r => {
          if (!r.ok) throw new Error(r.status);
          return r.text();
        })
        .then(html => {
          // Počkáme na minimální dobu loaderu
          const elapsedTime = Date.now() - startTime;
          const remainingTime = Math.max(0, minLoaderTime - elapsedTime);

          setTimeout(() => {
            adminSection.innerHTML = html;
            adminSection.style.opacity = '1';
            initTabs();
            loadSectionScript(section);

            // Speciální inicializace pro příspěvky
            if (section === 'prispevky') {
              initPostsSection();
            }
            // Speciální inicializace pro uživatele
            else if (section === 'uzivatele') {
              initUsersSection();
            }
            // Speciální inicializace pro obrázky
            else if (section === 'obrazky') {
              initImagesSection();
            }

            // Loader se skryje automaticky po dokončení simulace
          }, remainingTime);
        })
        .catch(e => {
          adminSection.innerHTML = `<p class="error-message">Chyba: ${e.message}</p>`;
          adminSection.style.opacity = '1';
          blkt_notifikace('Nepodařilo se načíst sekci', 'error');
          AdminLoader.hide();
        });
  }

  // ============================================
  // 6) Sekce PŘÍSPĚVKY - Přehled
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
          obsah:     row.dataset.obsah,
          slug:      row.dataset.slug,
          tags:      row.dataset.tags
        });
      };
    }
    if (addBtn) addBtn.onclick = () => loadPrispevkyEditor();
  }

  // ============================================
  // 7) Sekce PŘÍSPĚVKY - Editor
  // ============================================
  function loadPrispevkyEditor(data = {}) {
    // Změníme aktivní záložku na Editor
    const tabsNav = document.querySelector('.blkt-tabs');
    if (tabsNav) {
      const editorTab = tabsNav.querySelector('button[data-tab="editor"]');
      if (editorTab) {
        tabsNav.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
        editorTab.classList.add('active');
      }
    }

    // Přepneme obsah
    const tabPrehled = document.getElementById('tab-prehled');
    const tabEditor = document.getElementById('tab-editor');
    if (tabPrehled) tabPrehled.style.display = 'none';
    if (tabEditor) tabEditor.style.display = '';

    // Inicializujeme editor
    initEditorPrispevku(data);
  }

  function initEditorPrispevku(data = {}) {
    const titleEl = document.getElementById('blkt-post-title');
    const catEl   = document.getElementById('blkt-post-category');
    const slugEl  = document.getElementById('blkt-post-slug');
    const tagsEl  = document.getElementById('blkt-post-tags');

    if (titleEl) titleEl.value = data.nazev || '';
    if (catEl)   catEl.value   = data.kategorie || '';
    if (slugEl)  slugEl.value  = data.slug || '';
    if (tagsEl)  tagsEl.value  = data.tags || '';

    // Generování slugu z názvu
    if (titleEl && slugEl && !data.id) {
      titleEl.addEventListener('input', e => {
        slugEl.value = blkt_convertToSlug(e.target.value);
      });
    }

    // Počkáme na TinyMCE
    if (typeof tinymce === 'undefined') {
      setTimeout(() => initEditorPrispevku(data), 100);
      return;
    }

    tinymce.remove('#blkt-editor');
    tinymce.init({
      selector: '#blkt-editor',
      height:   '60vh',
      menubar:  false,
      branding: false,
      license_key: 'gpl',
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
        ed.on('init', () => {
          if (data.obsah) ed.setContent(data.obsah);
        });
        ed.ui.registry.addButton('gallery', {
          tooltip:'Galerie',
          icon:'browse',
          onAction:()=>blkt_openGalleryModal(ed)
        });
        ed.ui.registry.addButton('customImage', {
          tooltip:'Vložit obrázek',
          icon:'image',
          onAction:()=>blkt_openImageModal(ed)
        });
        ed.on('DblClick', e => {
          if (e.target.nodeName==='IMG') blkt_openImageModal(ed, e.target);
        });
      }
    });

    const cancelBtn = document.getElementById('blkt-post-cancel');
    const saveBtn   = document.getElementById('blkt-post-save');

    if (cancelBtn) cancelBtn.onclick = () => {
      const tabPrehled = document.getElementById('tab-prehled');
      const tabEditor = document.getElementById('tab-editor');
      const tabsNav = document.querySelector('.blkt-tabs');

      if (tabPrehled) tabPrehled.style.display = '';
      if (tabEditor) tabEditor.style.display = 'none';

      if (tabsNav) {
        const prehledTab = tabsNav.querySelector('button[data-tab="prehled"]');
        if (prehledTab) {
          tabsNav.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
          prehledTab.classList.add('active');
        }
      }
    };

    if (saveBtn) saveBtn.onclick = () => {
      const p = new FormData();
      p.append('blkt_id',        data.id || '');
      p.append('blkt_nazev',     document.getElementById('blkt-post-title').value);
      p.append('blkt_kategorie', document.getElementById('blkt-post-category').value);
      p.append('blkt_obsah',     tinymce.get('blkt-editor').getContent());
      p.append('blkt_slug',      document.getElementById('blkt-post-slug').value);
      p.append('blkt_tags',      document.getElementById('blkt-post-tags').value);

      fetch(`action/${data.id?'edit_prispevek':'add_prispevek'}.php`, { method:'POST', body:p })
          .then(r => r.json())
          .then(j => {
            if (j.status==='ok') {
              blkt_notifikace('Příspěvek byl uložen', 'success');
              // Přepneme zpět na přehled
              if (cancelBtn) cancelBtn.click();
              // Obnovíme seznam příspěvků
              refreshPrispevky();
            } else {
              blkt_notifikace('Chyba: ' + j.error, 'error');
            }
          })
          .catch(e => blkt_notifikace('Síťová chyba: ' + e.message, 'error'));
    };
  }

  // Pomocná funkce pro generování slug
  function blkt_convertToSlug(text) {
    return text
        .toString()
        .normalize('NFD')
        .replace(/\p{Diacritic}/gu, '')
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9 -]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
  }

  // Obnovení seznamu příspěvků
  function refreshPrispevky() {
    fetch('content/prispevky.php')
        .then(r => r.text())
        .then(html => {
          const tmp = document.createElement('div');
          tmp.innerHTML = html;
          const newTable = tmp.querySelector('#posts-table');
          const oldTable = document.querySelector('#posts-table');
          if (newTable && oldTable) {
            oldTable.replaceWith(newTable);
            initPostsSection(); // Znovu přiřadíme event listenery
          }
        });
  }

  // ============================================
  // 8) Sekce UŽIVATELE
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

    if (table) {
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
    }

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

    function closeModal(){
      overlay.style.display='none';
      modal.style.display='none';
    }

    if (addBtn) addBtn.onclick = () => showModal('add');
    if (editBtn) editBtn.onclick = () => {
      const id=document.getElementById('card-id').value;
      showModal('edit',{ id,
        jmeno:document.getElementById('card-jmeno').textContent,
        prijmeni:document.getElementById('card-prijmeni').textContent,
        mail:document.getElementById('card-mail').textContent,
        admin:document.getElementById('card-admin-text').textContent==='Ano'?'1':'0'
      });
    };
    if (delBtn) delBtn.onclick = () => {
      const id=document.getElementById('card-id').value;
      showModal('delete',{ id,
        jmeno:document.getElementById('card-jmeno').textContent,
        prijmeni:document.getElementById('card-prijmeni').textContent
      });
    };

    if (form) {
      form.onsubmit = e => {
        e.preventDefault();
        fetch(form.action,{method:'POST',body:new FormData(form)})
            .then(r=>r.json()).then(j=>{
          if(j.status==='ok'){
            blkt_notifikace(j.message || 'Operace úspěšná', 'success');
            closeModal();
            loadSection('uzivatele');
          }
          else blkt_notifikace('Chyba: '+j.error, 'error');
        }).catch(e=>blkt_notifikace('Síťová chyba: '+e.message, 'error'));
      };
    }
  }

  // ============================================
  // 9) Sekce OBRÁZKY
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
    if (addBtn) {
      addBtn.addEventListener('click',()=>{
        form.reset(); form.action='action/add_image.php';
        preview.src=''; preview.style.display='none'; zone.style.display='flex';
        document.getElementById('blkt-modal-title').textContent='Přidat obrázek';
        overlay.style.display='block'; modal.style.display='block';
      });
    }

    // zavření
    [overlay,closeBtn,cancelBtn].forEach(el=>{
      if(el) el.addEventListener('click',()=>{
        overlay.style.display='none'; modal.style.display='none';
      });
    });

    // upload zóna
    if (zone) {
      zone.addEventListener('click',()=>fileIn.click());
      ['dragenter','dragover'].forEach(evt=>zone.addEventListener(evt,e=>{
        e.preventDefault();zone.classList.add('blkt-upload-over');
      }));
      ['dragleave','drop'].forEach(evt=>zone.addEventListener(evt,e=>{
        e.preventDefault();zone.classList.remove('blkt-upload-over');
      }));
      zone.addEventListener('drop',e=>handleFile(e.dataTransfer.files[0]));
    }

    if (fileIn) {
      fileIn.addEventListener('change',()=>handleFile(fileIn.files[0]));
    }

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
    if (form) {
      form.addEventListener('submit',e=>{
        e.preventDefault();
        fetch(form.action,{method:'POST',body:new FormData(form)})
            .then(r=>r.json()).then(j=>{
          if(j.status==='ok'){
            blkt_notifikace('Obrázek uložen.', 'success');
            overlay.style.display='none'; modal.style.display='none';
            loadSection('obrazky');
          } else blkt_notifikace('Chyba:'+j.error, 'error');
        }).catch(e=>blkt_notifikace('Síťová chyba:'+e.message, 'error'));
      });
    }

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
            else blkt_notifikace('Chyba:'+j.error, 'error');
          }).catch(e=>blkt_notifikace('Síťová chyba:'+e.message, 'error'));
        }
      });
    });
  }

  // ============================================
  // 10) Event listenery
  // ============================================

  // Event listener na boční menu
  menuItems.forEach(item => {
    item.addEventListener('click', () => {
      loadSection(item.dataset.section);
    });
  });

  // Zpracování změny URL (tlačítka zpět/vpřed)
  window.addEventListener('popstate', (event) => {
    const sekce = event.state?.sekce || blkt_ziskej_sekci_z_url();
    loadSection(sekce, false); // false = neaktualizovat URL
  });

  // ============================================
  // 11) Globální funkce pro sekce
  // ============================================

  // Vystavíme loadSection globálně pro použití v jiných skriptech
  window.loadSection = loadSection;

  // ============================================
  // 12) Načtení výchozí sekce podle URL
  // ============================================
  const pocatecniSekce = blkt_ziskej_sekci_z_url();
  loadSection(pocatecniSekce, false);

}); // konec DOMContentLoaded