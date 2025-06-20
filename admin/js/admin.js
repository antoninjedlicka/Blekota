// admin/js/admin.js
// Kompletní administrační sekce s AJAX + Tabs + Notifikace + Responsivní záložky + LOADER
// Aktualizovaná verze se standardními modaly

document.addEventListener('DOMContentLoaded', () => {
  // ============================================
  // FUNKCE PRO PRÁCI S BARVAMI - PŘIDAT NA ZAČÁTEK
  // ============================================

// Funkce pro aplikaci barevného schématu na administraci
  window.blkt_aplikuj_barevne_schema = function(color) {
    console.log('Aplikuji barevné schéma:', color);

    // Vytvoříme nebo aktualizujeme style tag
    let styleTag = document.getElementById('blkt-dynamic-theme');
    if (!styleTag) {
      styleTag = document.createElement('style');
      styleTag.id = 'blkt-dynamic-theme';
      document.head.appendChild(styleTag);
    }

    // Generujeme odstíny barvy
    const shades = blkt_generuj_odstiny(color);

    // CSS proměnné pro dynamické téma - OPRAVENÉ, aby neměnily pozadí kde nemají
    styleTag.textContent = `
      :root {
        --blkt-primary: ${shades.primary};
        --blkt-primary-dark: ${shades.dark};
        --blkt-primary-light: ${shades.light};
        --blkt-primary-lighter: ${shades.lighter};
        --blkt-primary-shadow: ${shades.shadow};
      }
      
      /* Pouze barvy textu */
      .menu-item.active,
      .blkt-tabs button.active,
      .dashboard-stats li strong,
      h2, h3, h4, h5, h6,
      .blkt-cv-pozice-header h4,
      .month-divider span,
      a {
        color: var(--blkt-primary) !important;
      }
      
      /* Menu item active - pouze gradient */
      .menu-item.active {
        background: linear-gradient(135deg, var(--blkt-primary), var(--blkt-primary-light)) !important;
        color: white !important;
      }
      
      /* Tlačítka - zachovat původní definice */
      .btn-new-user, .btn-edit-user, .btn-new-post,
      .blkt-tlacitko-novy,
      #blkt-vybrat-obrazky,
      #blkt-pridat-uvitani,
      #blkt-vybrat-foto,
      #blkt-pridat-jazyk,
      #blkt-pridat-vzdelani,
      #blkt-pridat-vlastnost,
      #blkt-pridat-dovednost,
      #blkt-pridat-profesi {
        background: linear-gradient(135deg, var(--blkt-primary), var(--blkt-primary-light)) !important;
      }
      
      /* Focus stavy */
      input:focus, select:focus, textarea:focus {
        border-color: var(--blkt-primary) !important;
        box-shadow: 0 0 0 4px ${shades.lighter} !important;
      }
      
      input:focus + label,
      select:focus + label,
      textarea:focus + label,
      input:not(:placeholder-shown) + label,
      textarea:not(:placeholder-shown) + label,
      .blkt-formular-skupina select + label {
        color: var(--blkt-primary) !important;
      }
      
      /* Záložky - pouze aktivní */
      .blkt-tabs button.active {
        color: var(--blkt-primary) !important;
      }
      
      .blkt-tabs button::after,
      .blkt-tabs button.active::after {
        background: linear-gradient(90deg, var(--blkt-primary), var(--blkt-primary-light)) !important;
      }
      
      /* Upload zóna */
      .blkt-upload-zone {
        border-color: var(--blkt-primary) !important;
      }
      
      /* Galerie thumby */
      .blkt-gallery-thumb:hover,
      .blkt-gallery-thumb-modal:hover {
        border-color: var(--blkt-primary) !important;
      }
      
      .blkt-gallery-thumb.selected,
      .blkt-gallery-thumb-modal.blkt-vybrano {
        border-color: var(--blkt-primary) !important;
        box-shadow: 0 0 0 4px ${shades.lighter} !important;
      }
      
      /* Menu item hover - pouze levý pruh */
      .menu-item::before {
        background: var(--blkt-primary) !important;
      }
      
      /* Dashboard stats */
      .dashboard-stats li {
        border-left-color: var(--blkt-primary) !important;
      }
      
      /* Admin box h2 podtržení */
      .blkt-admin-box h2::after {
        content: '';
        display: block;
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, var(--blkt-primary), var(--blkt-primary-light));
        margin-top: 0.5rem;
      }
      
      /* Notifikace info */
      .blkt-notifikace-info {
        border-left-color: var(--blkt-primary) !important;
      }
      
      .blkt-notifikace-info .blkt-notifikace-ikona {
        background: linear-gradient(135deg, var(--blkt-primary), var(--blkt-primary-light)) !important;
      }
      
      /* Scrollbar */
      ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--blkt-primary), var(--blkt-primary-light)) !important;
      }
      
      ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, var(--blkt-primary-dark), var(--blkt-primary)) !important;
      }
      
      /* Výběr textu */
      ::selection {
        background: var(--blkt-primary) !important;
        color: white !important;
      }
      
      ::-moz-selection {
        background: var(--blkt-primary) !important;
        color: white !important;
      }
      
      /* Tabulka hlavička */
      thead {
        background: linear-gradient(135deg, var(--blkt-primary), var(--blkt-primary-light)) !important;
      }
      
      /* Color picker tlačítko */
      .blkt-color-picker-btn:hover {
        border-color: var(--blkt-primary) !important;
      }
      
      /* Měsíční rozdělení čáry */
      .month-divider::before,
      .month-divider::after {
        background: linear-gradient(90deg, transparent, var(--blkt-primary), transparent) !important;
      }
      
      /* Loader barvy */
      .blkt-loader-logo svg stop:first-child {
        stop-color: var(--blkt-primary) !important;
      }
      
      .blkt-loader-logo svg stop:last-child {
        stop-color: var(--blkt-primary-light) !important;
      }
      
      .blkt-loading-dots span {
        color: var(--blkt-primary) !important;
      }
      
      .blkt-loader-progress-bar {
        background: linear-gradient(90deg, var(--blkt-primary), var(--blkt-primary-light)) !important;
      }
      
      /* Odkazy podtržení */
      a::after {
        background: linear-gradient(90deg, var(--blkt-primary), var(--blkt-primary-light)) !important;
      }
      
      /* Citace */
      blockquote {
        border-left-color: var(--blkt-primary) !important;
      }
      
      /* User avatar */
      .user-avatar {
        border-color: var(--blkt-primary) !important;
      }
    `;
  };

  // Pomocné funkce pro práci s barvami
  window.blkt_generuj_odstiny = function(color) {
    // Převod hex na RGB
    const hex2rgb = (hex) => {
      const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
      return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
      } : null;
    };

    // Převod RGB na hex
    const rgb2hex = (r, g, b) => {
      return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    };

    const rgb = hex2rgb(color);
    if (!rgb) return {
      primary: color,
      dark: color,
      light: color,
      lighter: color,
      shadow: 'rgba(0,0,0,0.3)'
    };

    // Tmavší odstín (80% původní barvy)
    const dark = rgb2hex(
        Math.floor(rgb.r * 0.8),
        Math.floor(rgb.g * 0.8),
        Math.floor(rgb.b * 0.8)
    );

    // Světlejší odstín (směs s bílou)
    const light = rgb2hex(
        Math.min(255, Math.floor(rgb.r + (255 - rgb.r) * 0.3)),
        Math.min(255, Math.floor(rgb.g + (255 - rgb.g) * 0.3)),
        Math.min(255, Math.floor(rgb.b + (255 - rgb.b) * 0.3))
    );

    // Ještě světlejší pro pozadí
    const lighter = rgb2hex(
        Math.min(255, Math.floor(rgb.r + (255 - rgb.r) * 0.9)),
        Math.min(255, Math.floor(rgb.g + (255 - rgb.g) * 0.9)),
        Math.min(255, Math.floor(rgb.b + (255 - rgb.b) * 0.9))
    );

    // Stín
    const shadow = `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.4)`;

    return {
      primary: color,
      dark: dark,
      light: light,
      lighter: lighter,
      shadow: shadow
    };
  };

  // Funkce pro získání kontrastní barvy (černá/bílá)
  window.blkt_ziskej_kontrastni_barvu = function(color) {
    const hex = color.replace('#', '');
    const r = parseInt(hex.substr(0, 2), 16);
    const g = parseInt(hex.substr(2, 2), 16);
    const b = parseInt(hex.substr(4, 2), 16);
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    return luminance > 0.5 ? '#000000' : '#ffffff';
  };
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
  // 1) Galerie obrázků pro TinyMCE - UPRAVENO PRO STANDARDNÍ MODAL
  // ============================================
  window.blkt_openGalleryModal = function(editor) {
    console.log('[GalleryModal] Opening');

    const overlay = document.getElementById('blkt-gallery-overlay');
    const modal = document.getElementById('blkt-gallery-modal');
    const galleryEl = modal.querySelector('.blkt-gallery-images');
    const btnInsert = document.getElementById('blkt-gallery-insert');

    let selectedUrl = '', selectedAlt = '';

    // Použijeme standardní funkci pro otevření
    if (typeof blkt_otevri_modal === 'function') {
      blkt_otevri_modal('blkt-gallery-modal', 'blkt-gallery-overlay');
    } else {
      overlay.style.display = 'block';
      modal.style.display = 'block';
    }

    btnInsert.disabled = true;
    galleryEl.innerHTML = '<p>Načítám…</p>';

    fetch('action/list_images.php')
        .then(r => r.json())
        .then(list => {
          console.log('[GalleryModal] Loaded', list.length, 'images');
          galleryEl.innerHTML = '';
          list.forEach(img => {
            const thumb = document.createElement('img');
            thumb.src = img.url;
            thumb.alt = img.alt;
            thumb.title = img.title;
            thumb.className = 'blkt-gallery-thumb';
            thumb.addEventListener('click', () => {
              galleryEl.querySelectorAll('.selected').forEach(e => e.classList.remove('selected'));
              thumb.classList.add('selected');
              selectedUrl = img.url;
              selectedAlt = img.alt;
              btnInsert.disabled = false;
              console.log('[GalleryModal] Selected', selectedUrl);
            });
            galleryEl.append(thumb);
          });
        })
        .catch(() => {
          console.error('[GalleryModal] Error loading images');
          blkt_notifikace('Nepodařilo se načíst galerii.', 'error');
        });

    // Použijeme standardní close funkci
    modal.querySelector('.blkt-modal-close').onclick =
        document.getElementById('blkt-gallery-cancel').onclick = () => {
          console.log('[GalleryModal] Closing');
          if (typeof blkt_zavri_modal === 'function') {
            blkt_zavri_modal('blkt-gallery-modal', 'blkt-gallery-overlay');
          } else {
            overlay.style.display = 'none';
            modal.style.display = 'none';
          }
        };

    // Vložit
    btnInsert.onclick = () => {
      console.log('[GalleryModal] Inserting', selectedUrl);
      const align = document.getElementById('blkt-gallery-align').value;
      const disp = document.getElementById('blkt-gallery-display').value;
      let style = '';
      if (align==='left'||align==='right') style = `float:${align};margin:0 1em 1em 0;`;
      else if (align==='center') style = 'display:block;margin:0 auto 1em;';
      editor.insertContent(`<img src="${selectedUrl}" alt="${selectedAlt}" style="${style}display:${disp};">`);

      if (typeof blkt_zavri_modal === 'function') {
        blkt_zavri_modal('blkt-gallery-modal', 'blkt-gallery-overlay');
      } else {
        overlay.style.display = 'none';
        modal.style.display = 'none';
      }
    };
  };

  // ============================================
  // 2) Vložit / upravit obrázek pro TinyMCE
  // ============================================
  window.blkt_openImageModal = function(editor, existingImg) {
    console.log('[ImageModal] Dispatching to GalleryModal');
    window.blkt_openGalleryModal(editor);
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

    const script = document.createElement('script');
    script.id    = 'section-script';
    script.defer = true;
    script.src   = `js/${section}.js?v=${Date.now()}`;

    script.onload = () => {
      console.log(`Script ${section}.js načten`);

      // Pro nastavení zavoláme init funkci
      if (section === 'nastaveni' && typeof initNastaveniSection === 'function') {
        initNastaveniSection();
      }
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
            // PŘIDAT - Speciální inicializace pro nastavení
            else if (section === 'nastaveni') {
              setTimeout(() => {
                if (typeof initNastaveniSection === 'function') {
                  initNastaveniSection();
                }
              }, 100);
            }

            // DŮLEŽITÉ - Skrýt loader až po načtení
            AdminLoader.hide();
          }, remainingTime);
        })
        .catch(e => {
          adminSection.innerHTML = `<p class="error-message">Chyba: ${e.message}</p>`;
          adminSection.style.opacity = '1';
          blkt_notifikace('Nepodařilo se načíst sekci', 'error');
          // DŮLEŽITÉ - Skrýt loader i při chybě
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
  // 8) Sekce UŽIVATELE - UPRAVENO PRO STANDARDNÍ MODALY
  // ============================================
  function initUsersSection() {
    const table   = document.getElementById('users-table');
    const card    = document.getElementById('user-card');
    const toolbar = document.getElementById('user-toolbar');
    const addBtn  = document.getElementById('add-user-btn');
    const editBtn = document.getElementById('edit-user-btn');
    const delBtn  = document.getElementById('delete-user-btn');

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

    // Přidání nového uživatele
    if (addBtn) addBtn.onclick = () => showUserModal('add');

    // Úprava uživatele
    if (editBtn) editBtn.onclick = () => {
      const id = document.getElementById('card-id').value;
      showUserModal('edit', {
        id,
        jmeno: document.getElementById('card-jmeno').textContent,
        prijmeni: document.getElementById('card-prijmeni').textContent,
        mail: document.getElementById('card-mail').textContent,
        admin: document.getElementById('card-admin-text').textContent === 'Ano' ? '1' : '0'
      });
    };

    // Mazání uživatele - POUŽIJEME STANDARDNÍ POTVRZOVACÍ MODAL
    if (delBtn) delBtn.onclick = () => {
      const id = document.getElementById('card-id').value;
      const jmeno = document.getElementById('card-jmeno').textContent;
      const prijmeni = document.getElementById('card-prijmeni').textContent;

      // Použijeme standardní potvrzovací modal
      if (typeof blkt_potvrdit_akci === 'function') {
        blkt_potvrdit_akci(
            'Potvrďte smazání',
            `Opravdu chcete smazat uživatele <strong>${jmeno} ${prijmeni}</strong>?`,
            () => {
              // Akce po potvrzení
              const formData = new FormData();
              formData.append('blkt_id', id);

              const closeLoading = blkt_zobraz_loading ? blkt_zobraz_loading('Mažu uživatele...') : () => {};

              fetch('action/delete_user.php', {
                method: 'POST',
                body: formData
              })
                  .then(r => r.json())
                  .then(j => {
                    closeLoading();
                    if (j.status === 'ok') {
                      blkt_notifikace(j.message || 'Uživatel byl smazán', 'success');
                      loadSection('uzivatele'); // Obnovit seznam
                    } else {
                      blkt_notifikace('Chyba: ' + j.error, 'error');
                    }
                  })
                  .catch(e => {
                    closeLoading();
                    blkt_notifikace('Síťová chyba: ' + e.message, 'error');
                  });
            },
            null, // onCancel callback
            'danger' // typ modalu
        );
      } else {
        // Fallback pokud standardní modaly nejsou k dispozici
        if (confirm(`Opravdu chcete smazat uživatele ${jmeno} ${prijmeni}?`)) {
          const formData = new FormData();
          formData.append('blkt_id', id);

          fetch('action/delete_user.php', {
            method: 'POST',
            body: formData
          })
              .then(r => r.json())
              .then(j => {
                if (j.status === 'ok') {
                  blkt_notifikace(j.message || 'Uživatel byl smazán', 'success');
                  loadSection('uzivatele');
                } else {
                  blkt_notifikace('Chyba: ' + j.error, 'error');
                }
              });
        }
      }
    };

    // Funkce pro zobrazení user modalu - můžeme vytvořit dynamicky nebo použít existující
    function showUserModal(mode, data = {}) {
      // Pro jednoduchost použijeme existující overlay a modal
      // V budoucnu můžeme vytvořit standardní modal
      const overlay = document.getElementById('blkt-user-overlay') || createUserModal();
      const modal = document.getElementById('blkt-user-modal');
      const form = document.getElementById('blkt-user-form');
      const titleEl = document.getElementById('blkt-modal-title');
      const closeEl = document.getElementById('blkt-modal-close');

      if (!overlay || !modal) return;

      overlay.style.display = 'block';
      modal.style.display = 'block';

      titleEl.textContent = mode === 'add' ? 'Přidat uživatele' : 'Upravit uživatele';

      const html = `
        <input type="hidden" name="blkt_id" value="${data.id || ''}">
        <div class="blkt-formular-skupina">
          <input type="text" name="blkt_jmeno" placeholder=" " required value="${data.jmeno || ''}">
          <label>Jméno</label>
        </div>
        <div class="blkt-formular-skupina">
          <input type="text" name="blkt_prijmeni" placeholder=" " required value="${data.prijmeni || ''}">
          <label>Příjmení</label>
        </div>
        <div class="blkt-formular-skupina">
          <input type="email" name="blkt_mail" placeholder=" " required value="${data.mail || ''}">
          <label>E-mail</label>
        </div>
        <div class="blkt-formular-skupina">
          <select name="blkt_admin" required>
            <option value="0"${data.admin === '0' ? ' selected' : ''}>Ne</option>
            <option value="1"${data.admin === '1' ? ' selected' : ''}>Ano</option>
          </select>
          <label>Admin</label>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-cancel" id="blkt-user-cancel">Zrušit</button>
          <button type="submit" class="btn btn-save">${mode === 'add' ? 'Přidat' : 'Uložit'}</button>
        </div>
      `;

      form.action = `action/${mode}_user.php`;
      form.innerHTML = html;

      // Event listenery
      document.getElementById('blkt-user-cancel').onclick = closeUserModal;
      closeEl.onclick = closeUserModal;

      function closeUserModal() {
        overlay.style.display = 'none';
        modal.style.display = 'none';
      }

      // Submit
      form.onsubmit = e => {
        e.preventDefault();
        fetch(form.action, {method:'POST', body:new FormData(form)})
            .then(r=>r.json()).then(j=>{
          if(j.status==='ok'){
            blkt_notifikace(j.message || 'Operace úspěšná', 'success');
            closeUserModal();
            loadSection('uzivatele');
          }
          else blkt_notifikace('Chyba: '+j.error, 'error');
        }).catch(e=>blkt_notifikace('Síťová chyba: '+e.message, 'error'));
      };
    }

    // Vytvoření user modalu pokud neexistuje (pro zpětnou kompatibilitu)
    function createUserModal() {
      const adminContent = document.querySelector('.admin-content');
      if (!adminContent) return null;

      const overlayHtml = `<div id="blkt-user-overlay" class="blkt-modal-overlay" style="display:none;"></div>`;
      const modalHtml = `
        <div id="blkt-user-modal" class="blkt-modal medium" style="display:none;">
          <div class="blkt-modal-header">
            <h3 id="blkt-modal-title">Uživatel</h3>
            <button type="button" id="blkt-modal-close" class="blkt-modal-close">&times;</button>
          </div>
          <div class="blkt-modal-body">
            <form id="blkt-user-form" method="post"></form>
          </div>
        </div>
      `;

      adminContent.insertAdjacentHTML('beforeend', overlayHtml);
      adminContent.insertAdjacentHTML('beforeend', modalHtml);

      return document.getElementById('blkt-user-overlay');
    }
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
  // 12) Načtení a aplikace uloženého barevného schématu PŘED načtením sekce
  // ============================================
  // Načteme barvu HNED při startu a počkáme na ni
  fetch('action/get_theme_color.php')
      .then(r => r.json())
      .then(data => {
        console.log('Načtená barva z DB:', data);
        if (data.status === 'ok' && data.color) {
          blkt_aplikuj_barevne_schema(data.color);
        }
      })
      .catch(err => console.log('Nepodařilo se načíst barevné schéma:', err))
      .finally(() => {
        // ============================================
        // 13) Načtení výchozí sekce podle URL - AŽ PO načtení barvy
        // ============================================
        const pocatecniSekce = blkt_ziskej_sekci_z_url();
        loadSection(pocatecniSekce, false);
      });
  // ============================================
  // 13) Načtení výchozí sekce podle oprávnění
  // ============================================
  // Změníme načtení výchozí sekce podle URL na načtení první povolené sekce
  const pocatecniSekce = window.prvniPovolenaSekce || blkt_ziskej_sekci_z_url() || 'dashboard';
  loadSection(pocatecniSekce, false);
}); // konec DOMContentLoaded