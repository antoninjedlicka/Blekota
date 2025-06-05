// admin/js/admin.js
// Přepínání sekcí, inic. záložek a dynamické načítání sekčního JS

document.addEventListener('DOMContentLoaded', () => {
  const menuItems    = document.querySelectorAll('.menu-item');
  const adminSection = document.getElementById('admin-section');
  let currentSection = 'dashboard';

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
  function loadSection(section) {
    currentSection = section;
    adminSection.innerHTML = '<p style="text-align: center; padding: 60px 20px;">Načítám obsah zvolené sekce... momentíček!</p>';
    fetch(`content/${section}.php`)
      .then(r => { if (!r.ok) throw new Error(r.status); return r.text(); })
      .then(html => {
        adminSection.innerHTML = html;
        initTabs();
        loadSectionScript(section);
      })
      .catch(e => {
        adminSection.innerHTML = `<p class="error-message">Chyba: ${e.message}</p>`;
      });
  }

  // 4) Event listener na boční menu
  menuItems.forEach(item => {
    item.addEventListener('click', () => {
      menuItems.forEach(i => i.classList.remove('active'));
      item.classList.add('active');
      loadSection(item.dataset.section);
    });
  });

  // 5) Výchozí načtení Dashboard
  loadSection(currentSection);
});