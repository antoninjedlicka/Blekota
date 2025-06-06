// js/cv.js - JavaScript pro životopis

document.addEventListener('DOMContentLoaded', () => {
    console.log('[CV] Inicializace cv.js');

    // ========================================
    // 1. PLYNULÉ SCROLLOVÁNÍ PRO KOTVY
    // ========================================
    function blkt_inicializuj_plynule_scrollovani() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        console.log('[CV] Plynulé scrollování inicializováno');
    }

    // ========================================
    // 2. ANIMACE SEKCÍ PŘI SCROLLU
    // ========================================
    function blkt_inicializuj_animace_sekci() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                    // Po dokončení animace už nesledujeme
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Pozoruj všechny sekce
        document.querySelectorAll('.blkt-cv-sekce').forEach(section => {
            // Nastavíme animaci jako pozastavenou
            section.style.animationPlayState = 'paused';
            observer.observe(section);
        });

        console.log('[CV] Animace sekcí inicializovány');
    }

    // ========================================
    // 3. TLAČÍTKO PRO TISK CV
    // ========================================
    function blkt_vytvor_tlacitko_tisk() {
        const tlacitkoKontejner = document.createElement('div');
        tlacitkoKontejner.className = 'blkt-cv-tlacitka';
        tlacitkoKontejner.style.cssText = `
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            display: flex;
            gap: 1rem;
            z-index: 100;
        `;

        // Tlačítko tisk
        const tiskBtn = document.createElement('button');
        tiskBtn.className = 'blkt-cv-tlacitko-tisk';
        tiskBtn.innerHTML = '🖨️ Tisknout CV';
        tiskBtn.style.cssText = `
            background: #667eea;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        `;

        tiskBtn.addEventListener('mouseenter', () => {
            tiskBtn.style.transform = 'translateY(-2px)';
            tiskBtn.style.boxShadow = '0 6px 20px rgba(102, 126, 234, 0.4)';
        });

        tiskBtn.addEventListener('mouseleave', () => {
            tiskBtn.style.transform = 'translateY(0)';
            tiskBtn.style.boxShadow = '0 4px 12px rgba(102, 126, 234, 0.3)';
        });

        tiskBtn.addEventListener('click', () => {
            window.print();
        });

        tlacitkoKontejner.appendChild(tiskBtn);
        document.body.appendChild(tlacitkoKontejner);

        console.log('[CV] Tlačítko pro tisk vytvořeno');
    }

    // ========================================
    // 4. ANIMACE PROGRESS BARŮ PRO DOVEDNOSTI
    // ========================================
    function blkt_animuj_dovednosti() {
        const tags = document.querySelectorAll('.blkt-cv-tag');

        tags.forEach((tag, index) => {
            tag.style.opacity = '0';
            tag.style.transform = 'scale(0.8)';
            tag.style.transition = 'all 0.3s ease';

            // Postupná animace
            setTimeout(() => {
                tag.style.opacity = '1';
                tag.style.transform = 'scale(1)';
            }, index * 50);
        });

        console.log('[CV] Animace dovedností spuštěna');
    }

    // ========================================
    // 5. ANIMACE KONTAKTNÍCH INFORMACÍ
    // ========================================
    function blkt_animuj_kontakty() {
        const kontakty = document.querySelectorAll('.blkt-cv-kontakt li');

        kontakty.forEach((kontakt, index) => {
            kontakt.style.opacity = '0';
            kontakt.style.transform = 'translateX(-20px)';
            kontakt.style.transition = 'all 0.5s ease';

            setTimeout(() => {
                kontakt.style.opacity = '1';
                kontakt.style.transform = 'translateX(0)';
            }, 300 + (index * 100));
        });
    }

    // ========================================
    // 6. PARALAX EFEKT PRO HLAVIČKU
    // ========================================
    function blkt_inicializuj_paralax() {
        const hlavicka = document.querySelector('.blkt-cv-hlavicka');
        if (!hlavicka) return;

        let ticking = false;
        function blkt_aktualizuj_paralax() {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;

            if (scrolled < 300) {
                hlavicka.style.transform = `translateY(${rate}px)`;
            }

            ticking = false;
        }

        function blkt_pozadavek_paralax() {
            if (!ticking) {
                window.requestAnimationFrame(blkt_aktualizuj_paralax);
                ticking = true;
            }
        }

        window.addEventListener('scroll', blkt_pozadavek_paralax);
        console.log('[CV] Paralax efekt inicializován');
    }

    // ========================================
    // 7. DYNAMICKÉ POČÍTADLO PRO ROKY ZKUŠENOSTÍ
    // ========================================
    function blkt_spocitej_zkusenosti() {
        const prvniPrace = new Date('2008-07-01'); // První práce
        const dnes = new Date();
        const roky = Math.floor((dnes - prvniPrace) / (365.25 * 24 * 60 * 60 * 1000));

        // Vytvoř element pro zobrazení
        const zkusenostiElement = document.createElement('div');
        zkusenostiElement.className = 'blkt-cv-leta-praxe';
        zkusenostiElement.innerHTML = `
            <strong>${roky}+</strong> let praxe
        `;
        zkusenostiElement.style.cssText = `
            position: absolute;
            top: 2rem;
            right: 2rem;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 1.1em;
            color: white;
        `;

        const hlavicka = document.querySelector('.blkt-cv-hlavicka');
        if (hlavicka) {
            hlavicka.style.position = 'relative';
            hlavicka.appendChild(zkusenostiElement);
        }

        console.log(`[CV] Počet let praxe: ${roky}`);
    }

    // ========================================
    // 8. MOBILNÍ MENU PRO SEKCE
    // ========================================
    function blkt_vytvor_mobilni_menu() {
        if (window.innerWidth > 768) return;

        const sekce = document.querySelectorAll('.blkt-cv-sekce h2');
        if (sekce.length === 0) return;

        const menu = document.createElement('div');
        menu.className = 'blkt-cv-mobilni-menu';
        menu.style.cssText = `
            position: fixed;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 30px;
            padding: 0.5rem;
            display: flex;
            gap: 0.5rem;
            z-index: 100;
        `;

        sekce.forEach((sekceH2, index) => {
            const btn = document.createElement('button');
            btn.style.cssText = `
                background: #667eea;
                color: white;
                border: none;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                font-weight: bold;
                cursor: pointer;
            `;
            btn.textContent = index + 1;
            btn.title = sekceH2.textContent;

            btn.addEventListener('click', () => {
                sekceH2.parentElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });

            menu.appendChild(btn);
        });

        document.body.appendChild(menu);
        console.log('[CV] Mobilní menu vytvořeno');
    }

    // ========================================
    // 9. STAŽENÍ CV JAKO TEXT
    // ========================================
    function blkt_vytvor_tlacitko_stazeni() {
        const tlacitkoKontejner = document.querySelector('.blkt-cv-tlacitka');
        if (!tlacitkoKontejner) return;

        const stahnoutBtn = document.createElement('button');
        stahnoutBtn.className = 'blkt-cv-tlacitko-stahnout';
        stahnoutBtn.innerHTML = '⬇️ Stáhnout CV';
        stahnoutBtn.style.cssText = `
            background: #764ba2;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(118, 75, 162, 0.3);
            transition: all 0.3s ease;
        `;

        stahnoutBtn.addEventListener('mouseenter', () => {
            stahnoutBtn.style.transform = 'translateY(-2px)';
            stahnoutBtn.style.boxShadow = '0 6px 20px rgba(118, 75, 162, 0.4)';
        });

        stahnoutBtn.addEventListener('mouseleave', () => {
            stahnoutBtn.style.transform = 'translateY(0)';
            stahnoutBtn.style.boxShadow = '0 4px 12px rgba(118, 75, 162, 0.3)';
        });

        stahnoutBtn.addEventListener('click', blkt_stahni_cv_text);
        tlacitkoKontejner.appendChild(stahnoutBtn);
    }

    function blkt_stahni_cv_text() {
        let text = 'ANTONÍN JEDLIČKA - ŽIVOTOPIS\n';
        text += '='.repeat(40) + '\n\n';

        // Kontaktní údaje
        text += 'KONTAKT:\n';
        document.querySelectorAll('.blkt-cv-kontakt li').forEach(li => {
            text += '• ' + li.textContent.trim() + '\n';
        });
        text += '\n';

        // Sekce
        document.querySelectorAll('.blkt-cv-sekce').forEach(sekce => {
            const nadpis = sekce.querySelector('h2');
            if (nadpis) {
                text += nadpis.textContent.toUpperCase() + '\n';
                text += '-'.repeat(nadpis.textContent.length) + '\n';

                // Pozice
                sekce.querySelectorAll('.blkt-cv-pozice').forEach(pozice => {
                    const h3 = pozice.querySelector('h3');
                    const firma = pozice.querySelector('.blkt-cv-firma');
                    const datum = pozice.querySelector('.blkt-cv-datum');
                    const popis = pozice.querySelector('.blkt-cv-popis');

                    if (h3) text += '\n' + h3.textContent + '\n';
                    if (firma) text += firma.textContent + '\n';
                    if (datum) text += datum.textContent + '\n';
                    if (popis) text += popis.textContent + '\n';

                    pozice.querySelectorAll('ul li').forEach(li => {
                        text += '  - ' + li.textContent.trim() + '\n';
                    });
                });

                // Dovednosti
                sekce.querySelectorAll('.blkt-cv-dovednost-skupina').forEach(skupina => {
                    const h4 = skupina.querySelector('h4');
                    if (h4) {
                        text += '\n' + h4.textContent + ': ';
                        const tags = Array.from(skupina.querySelectorAll('.blkt-cv-tag'))
                            .map(tag => tag.textContent)
                            .join(', ');
                        text += tags + '\n';
                    }
                });

                text += '\n';
            }
        });

        // Stažení
        const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'antonin_jedlicka_cv.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        console.log('[CV] CV staženo jako text');
    }

    // ========================================
    // INICIALIZACE VŠECH FUNKCÍ
    // ========================================
    function blkt_inicializuj_cv() {
        blkt_inicializuj_plynule_scrollovani();
        blkt_inicializuj_animace_sekci();
        blkt_vytvor_tlacitko_tisk();
        blkt_animuj_kontakty();
        blkt_inicializuj_paralax();
        blkt_spocitej_zkusenosti();
        blkt_vytvor_mobilni_menu();
        blkt_vytvor_tlacitko_stazeni();

        // Animuj dovednosti až když jsou viditelné
        const dovednostiObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    blkt_animuj_dovednosti();
                    dovednostiObserver.disconnect();
                }
            });
        });

        const dovednostiSekce = document.querySelector('.blkt-cv-dovednosti');
        if (dovednostiSekce) {
            dovednostiObserver.observe(dovednostiSekce);
        }

        console.log('[CV] Všechny komponenty inicializovány');
    }

    // Spusť inicializaci
    blkt_inicializuj_cv();
});

// ========================================
// CSS PRO TISK
// ========================================
const tiskoveStyley = document.createElement('style');
tiskoveStyley.innerHTML = `
@media print {
    /* Skryj nepotřebné elementy */
    .blkt-hlavicka,
    .blkt-paticka,
    .blkt-cv-tlacitka,
    .blkt-cv-mobilni-menu,
    .blkt-loader {
        display: none !important;
    }
    
    /* Upravy pro tisk */
    body {
        background: white !important;
        animation: none !important;
    }
    
    .blkt-cv {
        margin: 0 !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        page-break-inside: avoid;
    }
    
    .blkt-cv-hlavicka {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
    
    .blkt-cv-sekce {
        page-break-inside: avoid;
        animation: none !important;
        opacity: 1 !important;
        transform: none !important;
    }
    
    .blkt-cv-pozice {
        page-break-inside: avoid;
    }
    
    /* Zachovej barvy */
    * {
        print-color-adjust: exact !important;
        -webkit-print-color-adjust: exact !important;
    }
}
`;
document.head.appendChild(tiskoveStyley);