// admin/js/modal-helpers.js
// Pomocné funkce pro práci se standardními modaly

/**
 * Otevře modal s daným ID
 * @param {string} modalId - ID modalu (bez #)
 * @param {string} overlayId - ID overlay (bez #), pokud není zadáno, použije se modalId + '-overlay'
 */
window.blkt_otevri_modal = function(modalId, overlayId = null) {
    const modal = document.getElementById(modalId);
    const overlay = document.getElementById(overlayId || modalId.replace('-modal', '-overlay'));

    if (modal && overlay) {
        overlay.style.display = 'block';
        modal.style.display = 'block';

        // Přidáme třídu pro animaci
        setTimeout(() => {
            modal.classList.add('active');
        }, 10);

        console.log(`Modal ${modalId} otevřen`);
        return true;
    }

    console.error(`Modal ${modalId} nebo overlay ${overlayId} nenalezen`);
    return false;
};

/**
 * Zavře modal s daným ID
 * @param {string} modalId - ID modalu (bez #)
 * @param {string} overlayId - ID overlay (bez #), pokud není zadáno, použije se modalId + '-overlay'
 */
window.blkt_zavri_modal = function(modalId, overlayId = null) {
    const modal = document.getElementById(modalId);
    const overlay = document.getElementById(overlayId || modalId.replace('-modal', '-overlay'));

    if (modal && overlay) {
        modal.classList.remove('active');

        // Počkáme na animaci před skrytím
        setTimeout(() => {
            overlay.style.display = 'none';
            modal.style.display = 'none';
        }, 300);

        console.log(`Modal ${modalId} zavřen`);
        return true;
    }

    console.error(`Modal ${modalId} nebo overlay ${overlayId} nenalezen`);
    return false;
};

/**
 * Vytvoří a zobrazí potvrzovací modal
 * @param {string} title - Nadpis modalu
 * @param {string} message - Zpráva k potvrzení
 * @param {function} onConfirm - Callback funkce po potvrzení
 * @param {function} onCancel - Callback funkce po zrušení (volitelné)
 * @param {string} type - Typ modalu: 'danger', 'warning', 'info' (výchozí: 'warning')
 */
window.blkt_potvrdit_akci = function(title, message, onConfirm, onCancel = null, type = 'warning') {
    // Odstraníme existující potvrzovací modal
    const existingModal = document.getElementById('blkt-confirm-modal');
    const existingOverlay = document.getElementById('blkt-confirm-overlay');
    if (existingModal) existingModal.remove();
    if (existingOverlay) existingOverlay.remove();

    // Ikony pro různé typy
    const icons = {
        danger: '⚠️',
        warning: '❓',
        info: 'ℹ️'
    };

    // Vytvoříme HTML strukturu
    const overlayHtml = `<div id="blkt-confirm-overlay" class="blkt-modal-overlay" style="display:block;"></div>`;
    const modalHtml = `
        <div id="blkt-confirm-modal" class="blkt-modal confirm ${type}" style="display:block;">
            <div class="blkt-modal-header">
                <h3>${title}</h3>
                <button type="button" class="blkt-modal-close">&times;</button>
            </div>
            <div class="blkt-modal-body">
                <span class="icon">${icons[type] || icons.warning}</span>
                <p>${message}</p>
            </div>
            <div class="blkt-modal-footer">
                <button type="button" class="btn btn-cancel" id="blkt-confirm-cancel">Zrušit</button>
                <button type="button" class="btn ${type === 'danger' ? 'btn-delete-user' : 'btn-save'}" id="blkt-confirm-ok">
                    ${type === 'danger' ? 'Smazat' : 'Potvrdit'}
                </button>
            </div>
        </div>
    `;

    // Přidáme do stránky
    const adminContent = document.querySelector('.admin-content');
    if (adminContent) {
        adminContent.insertAdjacentHTML('beforeend', overlayHtml);
        adminContent.insertAdjacentHTML('beforeend', modalHtml);

        // Přidáme event listenery
        const modal = document.getElementById('blkt-confirm-modal');
        const overlay = document.getElementById('blkt-confirm-overlay');
        const closeBtn = modal.querySelector('.blkt-modal-close');
        const cancelBtn = document.getElementById('blkt-confirm-cancel');
        const confirmBtn = document.getElementById('blkt-confirm-ok');

        // Funkce pro zavření
        const closeModal = () => {
            modal.remove();
            overlay.remove();
            if (onCancel) onCancel();
        };

        // Funkce pro potvrzení
        const confirmAction = () => {
            modal.remove();
            overlay.remove();
            if (onConfirm) onConfirm();
        };

        // Event listenery
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        confirmBtn.addEventListener('click', confirmAction);
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal();
        });

        // Escape klávesa
        const escapeHandler = (e) => {
            if (e.key === 'Escape') {
                closeModal();
                document.removeEventListener('keydown', escapeHandler);
            }
        };
        document.addEventListener('keydown', escapeHandler);

        console.log('Potvrzovací modal vytvořen');
    } else {
        console.error('Admin content nenalezen pro vložení modalu');
    }
};

/**
 * Vytvoří a zobrazí loading modal
 * @param {string} message - Zpráva k zobrazení (výchozí: "Načítám...")
 * @returns {function} Funkce pro zavření loading modalu
 */
window.blkt_zobraz_loading = function(message = 'Načítám...') {
    // Odstraníme existující loading modal
    const existingModal = document.getElementById('blkt-loading-modal');
    const existingOverlay = document.getElementById('blkt-loading-overlay');
    if (existingModal) existingModal.remove();
    if (existingOverlay) existingOverlay.remove();

    const overlayHtml = `<div id="blkt-loading-overlay" class="blkt-modal-overlay" style="display:block;"></div>`;
    const modalHtml = `
        <div id="blkt-loading-modal" class="blkt-modal loading small" style="display:block;">
            <div class="blkt-modal-body">
                ${message}
            </div>
        </div>
    `;

    const adminContent = document.querySelector('.admin-content');
    if (adminContent) {
        adminContent.insertAdjacentHTML('beforeend', overlayHtml);
        adminContent.insertAdjacentHTML('beforeend', modalHtml);

        console.log('Loading modal zobrazý');

        // Vrátíme funkci pro zavření
        return function() {
            const modal = document.getElementById('blkt-loading-modal');
            const overlay = document.getElementById('blkt-loading-overlay');
            if (modal) modal.remove();
            if (overlay) overlay.remove();
            console.log('Loading modal zavřen');
        };
    }

    return function() {}; // Prázdná funkce pokud se modal nepodařilo vytvořit
};

/**
 * Inicializuje standardní chování pro existující modaly
 * Přidá event listenery pro zavření přes X, overlay, nebo Escape
 * @param {string} modalId - ID modalu
 * @param {string} overlayId - ID overlay (volitelné)
 */
window.blkt_inicializuj_modal = function(modalId, overlayId = null) {
    const modal = document.getElementById(modalId);
    const overlay = document.getElementById(overlayId || modalId.replace('-modal', '-overlay'));

    if (!modal || !overlay) {
        console.error(`Modal ${modalId} nebo overlay nenalezen`);
        return;
    }

    const closeBtn = modal.querySelector('.blkt-modal-close');

    // Funkce pro zavření
    const closeModal = () => {
        blkt_zavri_modal(modalId, overlayId);
    };

    // Event listenery
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            closeModal();
        }
    });

    // Escape klávesa
    const escapeHandler = (e) => {
        if (e.key === 'Escape' && modal.style.display !== 'none') {
            closeModal();
        }
    };
    document.addEventListener('keydown', escapeHandler);

    console.log(`Modal ${modalId} inicializován`);
};

/**
 * Utility funkce pro změnu obsahu modalu
 * @param {string} modalId - ID modalu
 * @param {string} title - Nový nadpis (volitelné)
 * @param {string} body - Nový obsah těla (volitelné)
 */
window.blkt_zmen_obsah_modalu = function(modalId, title = null, body = null) {
    const modal = document.getElementById(modalId);
    if (!modal) {
        console.error(`Modal ${modalId} nenalezen`);
        return;
    }

    if (title) {
        const titleElement = modal.querySelector('.blkt-modal-header h3');
        if (titleElement) {
            titleElement.textContent = title;
        }
    }

    if (body) {
        const bodyElement = modal.querySelector('.blkt-modal-body');
        if (bodyElement) {
            bodyElement.innerHTML = body;
        }
    }

    console.log(`Obsah modalu ${modalId} aktualizován`);
};

console.log('Modal helpers načteny');