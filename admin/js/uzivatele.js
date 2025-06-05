// admin/js/uzivatele.js
// Inicializace tabulky uživatelů, modalu a kartu

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

  // Vyplnění karty při kliknutí na řádek tabulky
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

  // Otevření modalu pro add/edit/delete
  function showModal(mode,data={}) {
    overlay.style.display = 'block';
    modal.style.display   = 'block';
    titleEl.textContent   = mode==='add'?'Přidat uživatele'
                        : mode==='edit'?'Upravit uživatele'
                                       :'Potvrďte smazání';

    let html = '';
    if (mode === 'delete') {
      html = `
        <input type="hidden" name="blkt_id" value="${data.id}">
        <p>Smazat <strong>${data.jmeno} ${data.prijmeni}</strong>?</p>
        <div class="modal-actions">
          <button type="button" class="btn btn-cancel" id="blkt-cancel">Ne</button>
          <button type="submit" class="btn btn-save">Ano</button>
        </div>`;
    } else {
      html = `
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
          <button type="button" class="btn btn-cancel" id="blkt-cancel">Zrušit</button>
          <button type="submit" class="btn btn-save">${mode === 'add' ? 'Přidat' : 'Uložit'}</button>
        </div>`;
    }

    form.action = `action/${mode}_user.php`;
    form.innerHTML = html;
    document.getElementById('blkt-cancel').onclick = closeModal;
    closeEl.onclick = closeModal;
  }

  function closeModal() {
    overlay.style.display = 'none';
    modal.style.display   = 'none';
  }

  // Tlačítka Add/Edit/Delete
  if (addBtn)  addBtn.onclick  = () => showModal('add');
  if (editBtn) editBtn.onclick = () => {
    const id = document.getElementById('card-id').value;
    showModal('edit', {
      id,
      jmeno:    document.getElementById('card-jmeno').textContent,
      prijmeni: document.getElementById('card-prijmeni').textContent,
      mail:     document.getElementById('card-mail').textContent,
      admin:    document.getElementById('card-admin-text').textContent==='Ano'?'1':'0'
    });
  };
  if (delBtn)  delBtn.onclick  = () => {
    const id = document.getElementById('card-id').value;
    showModal('delete', {
      id,
      jmeno:    document.getElementById('card-jmeno').textContent,
      prijmeni: document.getElementById('card-prijmeni').textContent
    });
  };

  // Odeslání formuláře
  if (form) {
    form.onsubmit = e => {
      e.preventDefault();
      fetch(form.action, { method:'POST', body:new FormData(form) })
        .then(r => r.json())
        .then(j => {
          if (j.status==='ok') {
            alert('OK');
            closeModal();
            // znovu načteme sekci Uživatelé
            window.loadSection('uzivatele');
          } else {
            alert('Chyba: ' + j.error);
          }
        })
        .catch(err => alert('Síťová chyba: ' + err.message));
    };
  }
}

// Spustíme hned po načtení skriptu
initUsersSection();
