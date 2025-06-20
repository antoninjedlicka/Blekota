// admin/js/uzivatele.js
// Správa uživatelů a skupin - UPRAVENÁ VERZE

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
    document.getElementById('card-skupina').textContent = row.dataset.skupina || 'Bez skupiny';
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
      const row = table.querySelector(`tr[data-id="${id}"]`);
      if (row) {
        showUserModal('edit', {
          id,
          jmeno: row.dataset.jmeno,
          prijmeni: row.dataset.prijmeni,
          mail: row.dataset.mail,
          admin: row.dataset.admin,
          idskupiny: row.dataset.idskupiny
        });
      }
    };
  }

  // Smazání uživatele - POUŽIJEME STANDARDNÍ MODAL
  if (delBtn) {
    delBtn.onclick = () => {
      const id = document.getElementById('card-id').value;
      const jmeno = document.getElementById('card-jmeno').textContent;
      const prijmeni = document.getElementById('card-prijmeni').textContent;

      // Použijeme standardní potvrzovací modal
      if (typeof window.blkt_potvrdit_akci === 'function') {
        window.blkt_potvrdit_akci(
            'Smazat uživatele',
            `Opravdu chcete smazat uživatele <strong>${jmeno} ${prijmeni}</strong>?<br><br>Tato akce je nevratná.`,
            () => {
              // Akce po potvrzení
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
                      if (typeof window.loadSection === 'function') {
                        window.loadSection('uzivatele');
                      }
                    } else {
                      blkt_notifikace('Chyba: ' + j.error, 'error');
                    }
                  })
                  .catch(e => blkt_notifikace('Síťová chyba: ' + e.message, 'error'));
            },
            null, // onCancel callback
            'danger' // typ modalu
        );
      }
    };
  }
}

// Modal pro uživatele - UPRAVENÁ VERZE
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

  // Načteme seznam skupin pro select
  let skupinyOptions = '<option value="">Bez skupiny</option>';
  const skupinyRows = document.querySelectorAll('#groups-table tbody tr');
  skupinyRows.forEach(row => {
    const selected = data.idskupiny == row.dataset.id ? ' selected' : '';
    skupinyOptions += `<option value="${row.dataset.id}"${selected}>${row.dataset.nazev}</option>`;
  });

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
      <input type="password" name="blkt_heslo" placeholder=" " autocomplete="new-password">
      <label>${mode === 'add' ? 'Heslo' : 'Nové heslo (ponechte prázdné pro zachování)'}</label>
    </div>
    <div class="blkt-formular-skupina">
      <select name="blkt_idskupiny">
        ${skupinyOptions}
      </select>
      <label>Skupina</label>
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

            // Pokud bylo vygenerováno heslo, zobrazíme ho
            if (j.generated_password) {
              alert(`Bylo vygenerováno náhodné heslo: ${j.generated_password}\n\nNezapomeňte ho sdělit uživateli!`);
            }

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
// SPRÁVA SKUPIN A ROLÍ - zbytek kódu zůstává stejný
// ============================================
function initGroupManagement() {
  // ... původní kód ...
}

// Zajistíme, že funkce blkt_notifikace existuje
if (typeof window.blkt_notifikace === 'undefined') {
  window.blkt_notifikace = function(zprava, typ = 'info') {
    console.log(`[NOTIFIKACE ${typ}] ${zprava}`);
  };
}

// Spustit po načtení
initUsersSection();