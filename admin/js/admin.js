// admin/js/admin.js
// Přepínání sekcí, inic. záložek a dynamické načítání sekčního JS
// NOVĚ: URL routing + lepší notifikace

document.addEventListener('DOMContentLoaded', () => {
  const menuItems    = document.querySelectorAll('.menu-item');
  const adminSection = document.getElementById('admin-section');
  let currentSection = 'dashboard';

  // ============================================
  // NOVÉ: Systém notifikací
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
  // NOVÉ: URL routing
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
  // Původní funkce s úpravami
  // ============================================

  // 1) Přepínač statických záložek v právě načtené sekci
  function initTabs() {
    const tabsNav = document.querySelector('.blkt-tabs');
    if (!tabsNav) return;

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
    }

    tabs.forEach(tab => {
      tab.addEventListener('click', () => activateTab(tab));
    });
    if (tabs.length) activateTab(tabs[0]);
  }

  // 2) Dynamické načtení JS pro danou sekci
  function loadSectionScript(section) {
    const prev = document.getElementById('section-script');
    if (prev) prev.remove();
    const script = document.createElement('script');
    script.id    = 'section-script';
    script.defer = true;
    script.src   = `js/${section}.js`;
    document.body.appendChild(script);
  }

  // 3) Načtení a vykreslení sekce
  function loadSection(section, updateUrl = true) {
    currentSection = section;

    // Aktualizuj URL pouze pokud je to žádoucí
    if (updateUrl) {
      blkt_aktualizuj_url(section);
    }

    // Aktualizuj aktivní položku menu
    menuItems.forEach(item => {
      item.classList.toggle('active', item.dataset.section === section);
    });

    adminSection.innerHTML = '<p style="text-align: center; padding: 60px 20px;">Načítám obsah zvolené sekce... momentíček!</p>';

    fetch(`content/${section}.php`)
        .then(r => {
          if (!r.ok) throw new Error(r.status);
          return r.text();
        })
        .then(html => {
          adminSection.innerHTML = html;
          initTabs();
          loadSectionScript(section);
        })
        .catch(e => {
          adminSection.innerHTML = `<p class="error-message">Chyba: ${e.message}</p>`;
          blkt_notifikace('Nepodařilo se načíst sekci', 'error');
        });
  }

  // 4) Event listener na boční menu
  menuItems.forEach(item => {
    item.addEventListener('click', () => {
      loadSection(item.dataset.section);
    });
  });

  // 5) Zpracování změny URL (tlačítka zpět/vpřed)
  window.addEventListener('popstate', (event) => {
    const sekce = event.state?.sekce || blkt_ziskej_sekci_z_url();
    loadSection(sekce, false); // false = neaktualizovat URL
  });

  // 6) Načtení výchozí sekce podle URL
  const pocatecniSekce = blkt_ziskej_sekci_z_url();
  loadSection(pocatecniSekce, false);

  // ============================================
  // Globální funkce pro sekce
  // ============================================

  // Vystavíme loadSection globálně pro použití v jiných skriptech
  window.loadSection = loadSection;
});