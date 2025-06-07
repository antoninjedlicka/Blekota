function initHomepageSection() {
    const form = document.querySelector('.nastaveni-form');
    if (!form) return;

    // Přidání uvítacího textu
    document.getElementById('blkt-pridat-uvitani')?.addEventListener('click', () => {
        const li = document.createElement('li');
        li.innerHTML = `
            <input type="text" name="homepage_uvod[]" value="">
            <button type="button" class="blkt-odebrat-radek">✕</button>
        `;
        document.getElementById('blkt-uvitaci-texty').appendChild(li);
    });

    // Odebrání uvítacího textu
    document.getElementById('blkt-uvitaci-texty')?.addEventListener('click', e => {
        if (e.target.classList.contains('blkt-odebrat-radek')) {
            e.target.parentElement.remove();
        }
    });

    // Odebrání obrázku z galerie
    document.getElementById('blkt-galerie-vybrane')?.addEventListener('click', e => {
        if (e.target.classList.contains('blkt-galerie-odebrat')) {
            e.target.parentElement.remove();
        }
    });

    // Výběr obrázků z galerie
    document.getElementById('blkt-vybrat-obrazky')?.addEventListener('click', () => {
        blkt_openHomepageGallery();
    });

    // Odeslání formuláře AJAXem
    form.addEventListener('submit', e => {
        e.preventDefault();
        const fd = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            body: fd
        })
            .then(r => r.json())
            .then(j => {
                if (j.status === 'ok') {
                    blkt_notifikace('Nastavení homepage bylo uloženo.', 'success');
                } else {
                    blkt_notifikace('Chyba při ukládání: ' + j.error, 'error');
                }
            })
            .catch(err => {
                blkt_notifikace('Síťová chyba: ' + err.message, 'error');
            });
    });
}

// Funkce pro otevření galerie
function blkt_openHomepageGallery() {
    const overlay = document.getElementById('blkt-gallery-overlay');
    const modal   = document.getElementById('blkt-gallery-modal');
    const galleryEl = modal.querySelector('.blkt-gallery-images');
    overlay.style.display = modal.style.display = 'block';
    galleryEl.innerHTML = '<p>Načítám…</p>';

    fetch('action/list_images.php')
        .then(r => r.json())
        .then(list => {
            galleryEl.innerHTML = '';
            list.forEach(img => {
                const thumb = document.createElement('img');
                thumb.src = img.url;
                thumb.alt = img.alt;
                thumb.title = img.title;
                thumb.className = 'blkt-gallery-thumb';

                thumb.addEventListener('click', () => {
                    const container = document.getElementById('blkt-galerie-vybrane');
                    const current = container.querySelectorAll('.blkt-galerie-obrazek');
                    if (current.length >= 5) {
                        blkt_notifikace('Můžete vybrat maximálně 5 obrázků.', 'warning');
                        return;
                    }

                    // Kontrola duplicity
                    if ([...container.querySelectorAll('input')].some(i => i.value === img.url)) {
                        blkt_notifikace('Tento obrázek už je vybraný.', 'info');
                        return;
                    }

                    const wrap = document.createElement('div');
                    wrap.className = 'blkt-galerie-obrazek';
                    wrap.innerHTML = `
                        <input type="hidden" name="homepage_galerie[]" value="${img.url}">
                        <img src="${img.url}" class="galerie-nahled" data-src="${img.url}">
                        <button type="button" class="blkt-galerie-odebrat">&times;</button>
                    `;
                    container.appendChild(wrap);
                });

                galleryEl.appendChild(thumb);
            });
        })
        .catch(() => {
            blkt_notifikace('Nepodařilo se načíst obrázky z galerie.', 'error');
        });

    // Zavření modalu
    document.querySelector('.blkt-modal-close').onclick =
        document.getElementById('blkt-gallery-cancel').onclick = () => {
            overlay.style.display = modal.style.display = 'none';
        };
}

initHomepageSection();