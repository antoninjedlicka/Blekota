// main.js - Kompletní JavaScript pro celý web

document.addEventListener("DOMContentLoaded", function () {
    console.log('🚀 Hlavní JavaScript načten!');

    // ========================================
    // 1. LOADER
    // ========================================

    const loader = document.getElementById('blkt-loader');
    const loaderWrapper = document.querySelector('.blkt-loader-wrapper');
    const progressBar = document.querySelector('.blkt-loader-progress-bar');
    let progress = 0;
    let progressInterval;

    function showBlurOnly() {
        if (!loader) return;

        loader.style.display = 'flex';
        loader.style.visibility = 'visible';
        loader.style.opacity = '1';
        loader.classList.remove('blkt-loader--hidden', 'blkt-loader--invisible');

        if (loaderWrapper) {
            loaderWrapper.style.display = 'none';
        }
    }

    function showFullLoader() {
        if (!loader) return;

        loader.style.display = 'flex';
        loader.style.visibility = 'visible';
        loader.style.opacity = '1';
        loader.classList.remove('blkt-loader--hidden', 'blkt-loader--invisible');

        if (loaderWrapper) {
            loaderWrapper.style.display = 'block';
            loaderWrapper.style.opacity = '0';
            setTimeout(() => {
                loaderWrapper.style.transition = 'opacity 0.5s ease';
                loaderWrapper.style.opacity = '1';
            }, 50);
        }

        startProgress();
    }

    function hideLoader() {
        if (!loader) return;

        clearInterval(progressInterval);

        if (progressBar) {
            progressBar.style.width = '100%';
        }

        setTimeout(() => {
            loader.style.opacity = '0';

            setTimeout(() => {
                loader.style.display = 'none';
                loader.style.visibility = 'hidden';
                loader.classList.add('blkt-loader--hidden', 'blkt-loader--invisible');

                if (progressBar) {
                    progressBar.style.width = '0%';
                }
                if (loaderWrapper) {
                    loaderWrapper.style.display = 'block';
                    loaderWrapper.style.opacity = '1';
                    loaderWrapper.style.transition = '';
                }
                progress = 0;
            }, 300);
        }, 300);
    }

    function startProgress() {
        progress = 0;
        clearInterval(progressInterval);

        if (progressBar) {
            progressBar.style.width = '0%';
        }

        progressInterval = setInterval(() => {
            progress += Math.random() * 15 + 5;

            if (progress > 90) {
                progress = 90;
                clearInterval(progressInterval);
            }

            if (progressBar) {
                progressBar.style.width = progress + '%';
            }
        }, 300);
    }

    // ========================================
    // 2. HOMEPAGE FUNKCE
    // ========================================

    let homepageInterval = null;

    function blkt_inicializuj_homepage() {
        console.log('[Homepage] Inicializace');

        // Vyčistíme staré intervaly
        if (homepageInterval) {
            clearInterval(homepageInterval);
            homepageInterval = null;
        }

        // ANIMACE BOXŮ
        const boxy = document.querySelectorAll('.blkt-kontejner-vstup');
        boxy.forEach(box => box.classList.remove('blkt-zobrazit'));

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('blkt-zobrazit');
                    }, index * 150);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        boxy.forEach(box => observer.observe(box));

        // SLIDER
        const slider = document.querySelector('.blkt-slider');
        if (slider) {
            const oldIndicators = slider.querySelector('.blkt-slider-indikatory');
            if (oldIndicators) oldIndicators.remove();

            const obrazky = slider.querySelectorAll('img');
            let index = 0;
            let isPaused = false;

            const indikatory = document.createElement('div');
            indikatory.className = 'blkt-slider-indikatory';

            obrazky.forEach((_, i) => {
                const dot = document.createElement('span');
                dot.className = 'blkt-slider-dot';
                dot.addEventListener('click', () => {
                    index = i;
                    zobrazObrazek(index);
                });
                indikatory.appendChild(dot);
            });

            slider.appendChild(indikatory);

            function zobrazObrazek(novyIndex) {
                obrazky.forEach((img, i) => {
                    img.classList.toggle('blkt-slider-aktivni', i === novyIndex);
                });

                const dots = indikatory.querySelectorAll('.blkt-slider-dot');
                dots.forEach((dot, i) => {
                    dot.classList.toggle('aktivni', i === novyIndex);
                });
            }

            function autoPlay() {
                if (!isPaused) {
                    index = (index + 1) % obrazky.length;
                    zobrazObrazek(index);
                }
            }

            slider.addEventListener('mouseenter', () => {
                isPaused = true;
            });

            slider.addEventListener('mouseleave', () => {
                isPaused = false;
            });

            zobrazObrazek(index);
            homepageInterval = setInterval(autoPlay, 4000);
        }

        // ANIMACE TEXTŮ
        const texty = document.querySelectorAll('.blkt-homepage-text');
        texty.forEach((text, index) => {
            text.style.opacity = '0';
            text.style.transform = 'translateX(-20px)';

            setTimeout(() => {
                text.style.transition = 'all 0.6s ease';
                text.style.opacity = '1';
                text.style.transform = 'translateX(0)';
            }, 300 + (index * 100));
        });
    }

    // ========================================
    // 3. BLOG FUNKCE
    // ========================================

    function blkt_animuj_blog() {
        console.log('[Blog] Inicializace');

        const items = document.querySelectorAll('.blkt-masonry-item');
        items.forEach((el, index) => {
            el.style.animationDelay = `${index * 0.1}s`;
        });

        // Lazy loading obrázků
        const obrazky = document.querySelectorAll('.blkt-masonry-obrazek img');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.01
        });

        obrazky.forEach(img => imageObserver.observe(img));

        // Parallax pouze na PC
        if (window.innerWidth > 768) {
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                items.forEach((item, index) => {
                    const speed = 0.05 * (index % 2 === 0 ? 1 : -1);
                    const yPos = -(scrolled * speed);
                    if (Math.abs(yPos) < 50) {
                        item.style.transform = `translateY(${yPos}px)`;
                    }
                });
            });
        }
    }

    // ========================================
    // 4. CV FUNKCE
    // ========================================

    function blkt_inicializuj_cv() {
        console.log('[CV] Inicializace');

        // Animace sekcí
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

        // Animace kontaktů
        const kontakty = document.querySelectorAll('.blkt-cv-kontakt li');
        kontakty.forEach((kontakt, index) => {
            setTimeout(() => {
                kontakt.classList.add('blkt-animovano');
            }, 300 + (index * 100));
        });

        // Animace tagů
        const tags = document.querySelectorAll('.blkt-cv-tag');
        tags.forEach((tag, index) => {
            setTimeout(() => {
                tag.classList.add('blkt-animovano');
            }, index * 50);
        });

        // Tlačítka pro CV (tisk atd.) - pokud ještě neexistují
        if (!document.querySelector('.blkt-cv-tlacitka')) {
            const tlacitkoKontejner = document.createElement('div');
            tlacitkoKontejner.className = 'blkt-cv-tlacitka';

            const tiskBtn = document.createElement('button');
            tiskBtn.className = 'blkt-cv-tlacitko-tisk';
            tiskBtn.innerHTML = '🖨️ Tisknout CV';
            tiskBtn.addEventListener('click', () => window.print());

            tlacitkoKontejner.appendChild(tiskBtn);
            document.body.appendChild(tlacitkoKontejner);
        }
    }

    // ========================================
    // 5. INICIALIZACE PODLE STRÁNKY
    // ========================================

    function inicializujAktualniStranku() {
        // Homepage
        if (document.querySelector('.blkt-homepage-obsah')) {
            blkt_inicializuj_homepage();
        }

        // Blog
        if (document.querySelector('.blkt-blog-masonry')) {
            blkt_animuj_blog();
        }

        // CV
        if (document.querySelector('.blkt-cv')) {
            blkt_inicializuj_cv();
        }

        // Blog post - případné specifické funkce
        if (document.querySelector('.blkt-prispevek')) {
            // Zatím nic speciálního
        }
    }

    // ========================================
    // 6. SPA NAVIGACE
    // ========================================

    async function loadCSS(href) {
        return new Promise((resolve) => {
            const existing = document.querySelector(`link[href="${href}"]`);
            if (existing) {
                resolve();
                return;
            }

            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = href;
            link.onload = resolve;
            link.onerror = resolve;
            document.head.appendChild(link);
            setTimeout(resolve, 1000);
        });
    }

    function removeOldCSS() {
        document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
            const href = link.getAttribute('href');
            if (href && (href.includes('/homepage.css') ||
                href.includes('/blog.css') ||
                href.includes('/cv.css'))) {
                link.remove();
            }
        });
    }

    function vycistitStareIntervaly() {
        // Vyčistíme homepage interval
        if (homepageInterval) {
            clearInterval(homepageInterval);
            homepageInterval = null;
        }

        // Vyčistíme všechny timeouty a intervaly
        const highestId = window.setTimeout(function(){}, 0);
        for (let i = 0; i < highestId; i++) {
            window.clearTimeout(i);
            window.clearInterval(i);
        }
    }

    async function navigateTo(url) {
        console.log('🔄 Navigace na:', url);

        // Vyčistíme staré intervaly
        vycistitStareIntervaly();

        // Zobrazíme blur
        showBlurOnly();

        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Stránka nenalezena');

            const html = await response.text();
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');

            // Najdeme hlavní obsah
            const newMain = newDoc.querySelector('main');
            const currentMain = document.querySelector('main');

            if (!newMain || !currentMain) {
                throw new Error('Chybí hlavní obsah');
            }

            // Načteme CSS
            const newCSS = [];
            newDoc.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
                const href = link.getAttribute('href');
                if (href && (href.includes('/homepage.css') ||
                    href.includes('/blog.css') ||
                    href.includes('/cv.css'))) {
                    newCSS.push(href);
                }
            });

            // Fade out
            currentMain.style.transition = 'opacity 0.3s ease';
            currentMain.style.opacity = '0';

            await new Promise(resolve => setTimeout(resolve, 300));

            // Zobrazíme loader
            showFullLoader();

            // CSS
            removeOldCSS();
            for (const css of newCSS) {
                await loadCSS(css);
            }

            await new Promise(resolve => setTimeout(resolve, 100));

            // Vyměníme obsah
            currentMain.innerHTML = newMain.innerHTML;

            // Fade in
            currentMain.style.opacity = '0';
            requestAnimationFrame(() => {
                currentMain.style.transition = 'opacity 0.5s ease';
                currentMain.style.opacity = '1';
            });

            // Meta
            document.title = newDoc.title;
            history.pushState({}, newDoc.title, url);
            window.scrollTo(0, 0);

            // Aktualizujeme menu
            updateActiveLinks(url);

            // DŮLEŽITÉ: Reinicializujeme funkce pro novou stránku
            setTimeout(() => {
                inicializujAktualniStranku();
            }, 100);

            // Skryjeme loader
            setTimeout(hideLoader, 500);

            console.log('✅ Navigace dokončena');

        } catch (error) {
            console.error('❌ Chyba:', error);
            window.location.href = url;
        }
    }

    function updateActiveLinks(url) {
        const currentPath = new URL(url, window.location.origin).pathname;

        document.querySelectorAll('.blkt-navigace a').forEach(link => {
            const linkPath = new URL(link.href, window.location.origin).pathname;

            if (linkPath === currentPath ||
                (currentPath === '/' && link.textContent.includes('Domů'))) {
                link.classList.add('blkt-aktivni');
            } else {
                link.classList.remove('blkt-aktivni');
            }
        });
    }

    // ========================================
    // 7. EVENT LISTENERY
    // ========================================

    // Kliknutí na odkazy
    document.addEventListener('click', async function(e) {
        const link = e.target.closest('a');
        if (!link || !link.href) return;

        try {
            const url = new URL(link.href);

            if (url.origin !== window.location.origin ||
                link.target ||
                link.dataset.noSpa ||
                url.hash ||
                url.pathname.includes('/admin') ||
                url.pathname.includes('/login') ||
                url.pathname.includes('/logout') ||
                url.pathname.endsWith('.pdf')) {
                return;
            }

            e.preventDefault();
            await navigateTo(url.href);

        } catch (err) {
            console.error('Chyba:', err);
        }
    });

    // Browser navigace
    window.addEventListener('popstate', function() {
        navigateTo(window.location.href);
    });

    // První načtení
    window.addEventListener('load', function() {
        hideLoader();
        updateActiveLinks(window.location.href);
        inicializujAktualniStranku();
    });

    // ========================================
    // 8. EXPORT
    // ========================================

    window.blktMain = {
        navigateTo: navigateTo,
        showLoader: showFullLoader,
        hideLoader: hideLoader,
        inicializujHomepage: blkt_inicializuj_homepage,
        inicializujBlog: blkt_animuj_blog,
        inicializujCV: blkt_inicializuj_cv
    };
});