// admin/js/seo.js
// AJAX uložení SEO formuláře + živé doplňování + JSON náhled + uložení do DB

function initSeoSection() {
    const form = document.querySelector('.nastaveni-form');
    if (!form) return;

    // AJAX odeslání formuláře
    form.addEventListener('submit', e => {
        e.preventDefault();

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form)
        })
            .then(r => r.json())
            .then(j => {
                if (j.status === 'ok') {
                    alert('SEO nastavení bylo uloženo.');
                } else {
                    alert('Chyba při ukládání: ' + j.error);
                }
            })
            .catch(err => {
                alert('Síťová chyba: ' + err.message);
            });
    });

    // === DYNAMICKÉ DOPLŇOVÁNÍ HODNOT ===
    const poleZdroj = {
        seo_title: ['seo_og_title', 'seo_ld_name'],
        seo_description: ['seo_og_description'],
        seo_author: ['seo_ld_author']
    };

    Object.keys(poleZdroj).forEach(zdrojId => {
        const zdroj = document.querySelector(`[name="${zdrojId}"]`);
        if (!zdroj) return;

        zdroj.addEventListener('input', () => {
            poleZdroj[zdrojId].forEach(cilId => {
                const cil = document.querySelector(`[name="${cilId}"]`);
                if (cil && cil.value.trim() === '') {
                    cil.value = zdroj.value;
                }
            });
            aktualizujJsonLd();
        });
    });

    // === AKTUALIZACE JSON-LD ===
    document.querySelectorAll('[name]').forEach(el => {
        el.addEventListener('input', aktualizujJsonLd);
        el.addEventListener('change', aktualizujJsonLd);
    });

    function aktualizujJsonLd() {
        const get = id => document.querySelector(`[name="${id}"]`)?.value?.trim() || '';

        const json = {
            "@context": "https://schema.org",
            "@type": get('seo_ld_type') || 'WebSite',
            "name": get('seo_ld_name') || get('seo_title'),
            "url": seo_www_url,
            "author": {
                "@type": get('seo_ld_author_type') || 'Person',
                "name": get('seo_ld_author') || get('seo_author')
            },
            "description": get('seo_description')
        };

        const vystup = document.querySelector('textarea[readonly]');
        if (vystup) {
            vystup.value = JSON.stringify(json, null, 2);
        }

        const hidden = document.querySelector('[name="seo_ld_json"]');
        if (hidden) {
            hidden.value = JSON.stringify(json); // bez zarovnání pro DB
        }
    }

    aktualizujJsonLd(); // první vykreslení
}

// Globální proměnná – doplň do <script> v seo.php před tímto souborem:
seo_www_url = window.seo_www_url || '';
initSeoSection();
