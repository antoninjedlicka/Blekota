// admin/js/uzivatele.js
// Správa uživatelů a skupin

function initUsersSection() {
  console.log('initUsersSection() - start');

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
    }

    tabs.forEach(tab => {
      tab.addEventListener('click', () => activateTab(tab));
    });
  }

  // Inicializace záložky Přehled (uživatelé)
  initUserManagement();

  // Inicializace záložky Skupiny a role
  initGroupManagement();
}

// ============================================
// SPRÁVA UŽIVATELŮ
// ============================================
function initUserManagement() {
  const table = document.getElementById('users-table');
  const card = document.getElementById('user-card');
  const toolbar = document.getElementById('user-toolbar');
  const addBtn = document.getElementById('add-user-btn');
  const editBtn = document.getElementById('edit-user-btn');
  const delBtn = document.getElementById('delete-user-btn');

  if (!table) return;

  // Kliknutí na řádek tabulky
  table.addEventListener('click', e => {
    const row = e.target.closest('tr');
    if (!row || !row.dataset.id) return;

    // Označit aktivní řádek
    table.querySelectorAll('tr').forEach(r => r.classList.remove('active'));
    row.classList.add('active');

    // Zobrazit kartu uživatele
    document.getElementById('card-id').value = row.dataset.id;
    document.getElementById('card-jmeno').textContent = row.dataset.jmeno;
    document.getElementById('card-prijmeni').textContent = row.dataset.prijmeni;
    document.getElementById('card-mail').textContent = row.dataset.mail;
    document.getElementById('card-admin-text').textContent = row.dataset.admin === '1' ? 'Ano' : 'Ne';

    card.style.display = 'flex';
    toolbar.style.display = 'flex';
  });

  // Přidání uživatele
  if (addBtn) {
    addBtn.onclick = () => showUserModal('add');
  }

  // Úprava uživatele
  if (editBtn) {
    editBtn.onclick = () => {
      const id = document.getElementById('card-id').value;
      showUserModal('edit', {
        id,
        jmeno: document.getElementById('card-jmeno').textContent,
        prijmeni: document.getElementById('card-prijmeni').textContent,
        mail: document.getElementById('card-mail').textContent,
        admin: document.getElementById('card-admin-text').textContent === 'Ano' ? '1' : '0'
      });
    };
  }

  // Smazání uživatele
  if (delBtn) {
    delBtn.onclick = () => {
      const id = document.getElementById('card-id').value;
      const jmeno = document.getElementById('card-jmeno').textContent;
      const prijmeni = document.getElementById('card-prijmeni').textContent;

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
                // Reload sekce
                if (typeof window.loadSection === 'function') {
                  window.loadSection('uzivatele');
                }
              } else {
                blkt_notifikace('Chyba: ' + j.error, 'error');
              }
            })
            .catch(e => blkt_notifikace('Síťová chyba: ' + e.message, 'error'));
      }
    };
  }
}

// Modal pro uživatele
function showUserModal(mode, data = {}) {
  const overlay = document.getElementById('blkt-user-overlay');
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
    fetch(form.action, { method: 'POST', body: new FormData(form) })
        .then(r => r.json())
        .then(j => {
          if (j.status === 'ok') {
            blkt_notifikace(j.message || 'Operace úspěšná', 'success');
            closeUserModal();
            if (typeof window.loadSection === 'function') {
              window.loadSection('uzivatele');
            }
          } else {
            blkt_notifikace('Chyba: ' + j.error, 'error');
          }
        })
        .catch(e => blkt_notifikace('Síťová chyba: ' + e.message, 'error'));
  };
}

// ============================================
// SPRÁVA SKUPIN A ROLÍ
// ============================================
function initGroupManagement() {
  const table = document.getElementById('groups-table');
  const card = document.getElementById('group-card');
  const toolbar = document.getElementById('group-toolbar');
  const addBtn = document.getElementById('add-group-btn');
  const editBtn = document.getElementById('edit-group-btn');
  const delBtn = document.getElementById('delete-group-btn');

  if (!table) return;

  // Kliknutí na řádek tabulky
  table.addEventListener('click', e => {
    const row = e.target.closest('tr');
    if (!row || !row.dataset.id) return;

    // Označit aktivní řádek
    table.querySelectorAll('tr').forEach(r => r.classList.remove('active'));
    row.classList.add('active');

    // Zobrazit kartu skupiny
    document.getElementById('group-card-id').value = row.dataset.id;
    document.getElementById('group-card-nazev').textContent = row.dataset.nazev;
    document.getElementById('group-card-popis').textContent = row.dataset.popis || 'Bez popisu';

    // Zobrazit seznam rolí
    const roleList = document.getElementById('group-card-role-list');
    roleList.innerHTML = '';

    try {
      const roleNazvy = JSON.parse(row.dataset.roleNazvy || '[]');
      if (roleNazvy.length > 0) {
        roleNazvy.forEach(nazev => {
          const li = document.createElement('li');
          li.textContent = nazev;
          roleList.appendChild(li);
        });
      } else {
        roleList.innerHTML = '<li style="background: transparent; color: var(--blkt-text-light);">Žádné role nejsou přiřazeny</li>';
      }
    } catch (e) {
      console.error('Chyba při parsování rolí:', e);
    }

    card.style.display = 'block';
    toolbar.style.display = 'flex';
  });

  // Přidání skupiny
  if (addBtn) {
    addBtn.onclick = () => showGroupModal('add');
  }

  // Úprava skupiny
  if (editBtn) {
    editBtn.onclick = () => {
      const id = document.getElementById('group-card-id').value;
      const row = table.querySelector(`tr[data-id="${id}"]`);
      if (row) {
        showGroupModal('edit', {
          id,
          nazev: row.dataset.nazev,
          popis: row.dataset.popis,
          role: row.dataset.role
        });
      }
    };
  }

  // Smazání skupiny
  if (delBtn) {
    delBtn.onclick = () => {
      const id = document.getElementById('group-card-id').value;
      const nazev = document.getElementById('group-card-nazev').textContent;

      if (confirm(`Opravdu chcete smazat skupinu "${nazev}"?\n\nUživatelé ve skupině nebudou smazáni, ale budou bez skupiny.`)) {
        const formData = new FormData();
        formData.append('blkt_idskupiny', id);

        fetch('action/delete_skupina.php', {
          method: 'POST',
          body: formData
        })
            .then(r => r.json())
            .then(j => {
              if (j.status === 'ok') {
                blkt_notifikace(j.message || 'Skupina byla smazána', 'success');
                if (typeof window.loadSection === 'function') {
                  window.loadSection('uzivatele');
                }
              } else {
                blkt_notifikace('Chyba: ' + j.error, 'error');
              }
            })
            .catch(e => blkt_notifikace('Síťová chyba: ' + e.message, 'error'));
      }
    };
  }
}

// Modal pro skupiny
function showGroupModal(mode, data = {}) {
  const overlay = document.getElementById('blkt-group-overlay');
  const modal = document.getElementById('blkt-group-modal');
  const form = document.getElementById('blkt-group-form');
  const titleEl = document.getElementById('blkt-group-modal-title');
  const closeEl = document.getElementById('blkt-group-modal-close');

  if (!overlay || !modal) return;

  overlay.style.display = 'block';
  modal.style.display = 'block';

  titleEl.textContent = mode === 'add' ? 'Přidat skupinu' : 'Upravit skupinu';

  // Naplnit formulář daty
  document.getElementById('blkt-group-modal-id').value = data.id || '';
  document.getElementById('blkt-group-nazev').value = data.nazev || '';
  document.getElementById('blkt-group-popis').value = data.popis || '';

  // Nastavit checkboxy rolí
  const roleIds = data.role ? data.role.split(',').map(id => id.trim()) : [];
  document.querySelectorAll('.role-checkbox').forEach(checkbox => {
    checkbox.checked = roleIds.includes(checkbox.value);
  });

  // Event listenery
  const cancelBtn = document.getElementById('blkt-group-cancel');

  function closeGroupModal() {
    overlay.style.display = 'none';
    modal.style.display = 'none';
  }

  cancelBtn.onclick = closeGroupModal;
  closeEl.onclick = closeGroupModal;

  overlay.onclick = e => {
    if (e.target === overlay) closeGroupModal();
  };

  // Submit
  form.onsubmit = e => {
    e.preventDefault();

    const formData = new FormData(form);
    const action = mode === 'add' ? 'add_skupina.php' : 'edit_skupina.php';

    fetch(`action/${action}`, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(j => {
          if (j.status === 'ok') {
            blkt_notifikace(j.message || 'Operace úspěšná', 'success');
            closeGroupModal();
            if (typeof window.loadSection === 'function') {
              window.loadSection('uzivatele');
            }
          } else {
            blkt_notifikace('Chyba: ' + j.error, 'error');
          }
        })
        .catch(e => blkt_notifikace('Síťová chyba: ' + e.message, 'error'));
  };
}

// Zajistíme, že funkce blkt_notifikace existuje
if (typeof window.blkt_notifikace === 'undefined') {
  window.blkt_notifikace = function(zprava, typ = 'info') {
    console.log(`[NOTIFIKACE ${typ}] ${zprava}`);
  };
}

// Spustit po načtení
initUsersSection();