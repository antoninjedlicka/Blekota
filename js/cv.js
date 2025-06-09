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
                    entry.target.classList.add('blkt-animace-spustena');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.blkt-cv-sekce').forEach(section => {
            observer.observe(section);
        });

        console.log('[CV] Animace sekcí inicializovány');
    }

    // ========================================
    // 3. TLAČÍTKO PRO TISK CV - OPRAVENÉ PRO CELÝ DOKUMENT
    // ========================================
    function blkt_vytvor_tlacitko_tisk() {
        const tlacitkoKontejner = document.createElement('div');
        tlacitkoKontejner.className = 'blkt-cv-tlacitka';

        // Tlačítko tisk
        const tiskBtn = document.createElement('button');
        tiskBtn.className = 'blkt-cv-tlacitko-tisk';
        tiskBtn.innerHTML = '🖨️ Tisknout CV';

        tiskBtn.addEventListener('click', () => {
            // Uložíme původní styly
            const cvElement = document.querySelector('article.blkt-cv');
            const originalHeight = cvElement.style.height;
            const originalOverflow = cvElement.style.overflow;
            const bodyOverflow = document.body.style.overflow;

            // Dočasně změníme styly pro tisk
            cvElement.style.height = 'auto';
            cvElement.style.overflow = 'visible';
            document.body.style.overflow = 'visible';

            // Přidáme třídu pro tisk
            document.body.classList.add('blkt-tisk-cv');

            // Počkáme na překreslení
            setTimeout(() => {
                // Spustíme tisk
                window.print();

                // Obnovíme původní styly
                setTimeout(() => {
                    cvElement.style.height = originalHeight;
                    cvElement.style.overflow = originalOverflow;
                    document.body.style.overflow = bodyOverflow;
                    document.body.classList.remove('blkt-tisk-cv');
                }, 100);
            }, 100);
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
            setTimeout(() => {
                tag.classList.add('blkt-animovano');
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
            setTimeout(() => {
                kontakt.classList.add('blkt-animovano');
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
    // 7. DYNAMICKÉ POČÍTADLO PRO ROKY ZKUŠENOSTÍ - SPRÁVNÝ VÝPOČET
    // ========================================
    function blkt_spocitej_zkusenosti() {
        // Najdeme všechny pracovní pozice
        const vsechnyPozice = document.querySelectorAll('.blkt-cv-pozice .blkt-cv-datum');
        let nejstarsiRok = null;

        vsechnyPozice.forEach(pozice => {
            const text = pozice.textContent;
            // Hledáme všechny roky ve formátu YYYY
            const rokyMatch = text.match(/\b(19|20)\d{2}\b/g);

            if (rokyMatch && rokyMatch.length > 0) {
                rokyMatch.forEach(rok => {
                    const cisloRoku = parseInt(rok);
                    if (!nejstarsiRok || cisloRoku < nejstarsiRok) {
                        nejstarsiRok = cisloRoku;
                    }
                });
            }
        });

        // Pokud jsme nenašli žádný rok, zkusíme najít měsíc/rok
        if (!nejstarsiRok) {
            vsechnyPozice.forEach(pozice => {
                const text = pozice.textContent;
                // Hledáme formát MM/YYYY
                const mesicRokMatch = text.match(/\b\d{1,2}\/(\d{4})\b/g);

                if (mesicRokMatch && mesicRokMatch.length > 0) {
                    mesicRokMatch.forEach(datum => {
                        const rok = parseInt(datum.split('/')[1]);
                        if (!nejstarsiRok || rok < nejstarsiRok) {
                            nejstarsiRok = rok;
                        }
                    });
                }
            });
        }

        // Výchozí hodnota, pokud nic nenajdeme
        if (!nejstarsiRok) {
            nejstarsiRok = 2008;
        }

        const dnes = new Date();
        const aktualniRok = dnes.getFullYear();
        const roky = aktualniRok - nejstarsiRok;

        console.log(`[CV] Nalezené roky:`, vsechnyPozice.length, 'pozic');
        console.log(`[CV] Nejstarší rok: ${nejstarsiRok}`);
        console.log(`[CV] Počet let praxe: ${roky}`);

        const zkusenostiElement = document.createElement('div');
        zkusenostiElement.className = 'blkt-cv-leta-praxe';
        zkusenostiElement.innerHTML = `<strong>${roky}+</strong> let praxe`;

        const hlavicka = document.querySelector('.blkt-cv-hlavicka');
        if (hlavicka) {
            // Zkontrolujeme, jestli už element neexistuje
            const existujici = hlavicka.querySelector('.blkt-cv-leta-praxe');
            if (existujici) {
                existujici.remove();
            }
            hlavicka.appendChild(zkusenostiElement);
        }
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

        sekce.forEach((sekceH2, index) => {
            const btn = document.createElement('button');
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
    // 9. STAŽENÍ CV JAKO TEXT - OPRAVENÉ PRO FUNKČNÍ DOWNLOAD
    // ========================================
    function blkt_vytvor_tlacitko_stazeni() {
        const tlacitkoKontejner = document.querySelector('.blkt-cv-tlacitka');
        if (!tlacitkoKontejner) return;

        const stahnoutBtn = document.createElement('button');
        stahnoutBtn.className = 'blkt-cv-tlacitko-stahnout';
        stahnoutBtn.innerHTML = '⬇️ Stáhnout CV';

        stahnoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            blkt_stahni_cv_text();
        });

        tlacitkoKontejner.appendChild(stahnoutBtn);
    }

    function blkt_stahni_cv_text() {
        console.log('[CV] Začínám stahování CV');

        // Získáme jméno z hlavičky
        const jmenoElement = document.querySelector('.blkt-cv-info h1');
        const jmeno = jmenoElement ? jmenoElement.textContent.trim() : 'CV';

        let text = `${jmeno.toUpperCase()} - ŽIVOTOPIS\n`;
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
                    const obsah = pozice.querySelector('.blkt-cv-obsah');

                    if (h3) text += '\n' + h3.textContent + '\n';
                    if (firma) text += firma.textContent + '\n';
                    if (datum) text += datum.textContent + '\n';
                    if (popis) text += popis.textContent + '\n';

                    // Zpracování obsahu s HTML
                    if (obsah) {
                        // Převedeme HTML na text
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = obsah.innerHTML;

                        // Zpracujeme seznamy
                        tempDiv.querySelectorAll('ul li, ol li').forEach(li => {
                            text += '  - ' + li.textContent.trim() + '\n';
                        });

                        // Zpracujeme odstavce
                        tempDiv.querySelectorAll('p').forEach(p => {
                            const pText = p.textContent.trim();
                            if (pText && !p.closest('li')) {
                                text += pText + '\n';
                            }
                        });
                    }
                });

                // Dovednosti
                sekce.querySelectorAll('.blkt-cv-dovednost-skupina').forEach(skupina => {
                    const h4 = skupina.querySelector('h4');
                    if (h4) {
                        text += '\n' + h4.textContent + ': ';
                        const tags = Array.from(skupina.querySelectorAll('.blkt-cv-tag'))
                            .map(tag => tag.textContent.trim())
                            .join(', ');
                        text += tags + '\n';
                    }

                    // Pro jazyky
                    const uroven = skupina.querySelector('p');
                    if (uroven) {
                        text += '  Úroveň: ' + uroven.textContent.trim() + '\n';
                    }
                });

                // Vlastnosti
                sekce.querySelectorAll('.blkt-cv-vlastnost').forEach(vlastnost => {
                    const h4 = vlastnost.querySelector('h4');
                    const p = vlastnost.querySelector('p');

                    if (h4) {
                        text += '\n• ' + h4.textContent.trim();
                        if (p) {
                            text += ': ' + p.textContent.trim();
                        }
                        text += '\n';
                    }
                });

                text += '\n';
            }
        });

        // Přidáme datum vygenerování
        const dnes = new Date();
        text += '\n\n---\n';
        text += `Vygenerováno: ${dnes.toLocaleDateString('cs-CZ')} v ${dnes.toLocaleTimeString('cs-CZ')}\n`;

        console.log('[CV] Text připraven, délka:', text.length);

        try {
            // Vytvoření Blob s BOM pro správné UTF-8 kódování
            const BOM = '\uFEFF';
            const blob = new Blob([BOM + text], { type: 'text/plain;charset=utf-8' });

            // Vytvoření názvu souboru
            const safeJmeno = jmeno.toLowerCase()
                .normalize('NFD')
                .replace(/\p{Diacritic}/gu, '')
                .replace(/[^a-z0-9]/g, '_')
                .replace(/_+/g, '_')
                .replace(/^_|_$/g, '');

            const nazevSouboru = `${safeJmeno}_zivotopis.txt`;

            // Pokud prohlížeč podporuje download API
            if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                // Pro IE/Edge
                window.navigator.msSaveOrOpenBlob(blob, nazevSouboru);
            } else {
                // Pro ostatní prohlížeče
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = nazevSouboru;
                link.style.display = 'none';

                // Přidáme do dokumentu
                document.body.appendChild(link);

                // Simulujeme kliknutí
                link.click();

                // Počkáme a pak uklidíme
                setTimeout(() => {
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                    console.log('[CV] Úklid dokončen');
                }, 100);
            }

            console.log('[CV] Stažení spuštěno:', nazevSouboru);

        } catch (error) {
            console.error('[CV] Chyba při stahování:', error);
            alert('Nepodařilo se stáhnout CV. Zkuste to prosím znovu.');
        }
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