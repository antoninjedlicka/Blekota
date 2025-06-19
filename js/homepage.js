// homepage.js - JavaScript pro domovskou stránku

document.addEventListener('DOMContentLoaded', () => {

    // ======= 1. ANIMACE ZOBRAZENÍ BOXŮ =======
    const boxy = document.querySelectorAll('.blkt-kontejner-vstup');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('blkt-zobrazit');
                }, index * 150); // postupné zobrazení s prodlevou 150ms
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    boxy.forEach(box => observer.observe(box));

    // ======= 2. AUTOMATICKÝ SLIDER S INDIKÁTORY =======
    const slider = document.querySelector('.blkt-slider');
    if (slider) {
        const obrazky = slider.querySelectorAll('img');
        let index = 0;
        let isPaused = false;
        let interval;

        // Vytvoření indikátorů
        const indikatory = document.createElement('div');
        indikatory.className = 'blkt-slider-indikatory';

        obrazky.forEach((_, i) => {
            const dot = document.createElement('span');
            dot.className = 'blkt-slider-dot';
            dot.addEventListener('click', () => {
                index = i;
                zobrazObrazek(index);
                resetInterval();
            });
            indikatory.appendChild(dot);
        });

        slider.appendChild(indikatory);

        // Funkce pro zobrazení obrázku
        function zobrazObrazek(novyIndex) {
            obrazky.forEach((img, i) => {
                img.classList.toggle('blkt-slider-aktivni', i === novyIndex);
            });

            // Aktualizace indikátorů
            const dots = indikatory.querySelectorAll('.blkt-slider-dot');
            dots.forEach((dot, i) => {
                dot.classList.toggle('aktivni', i === novyIndex);
            });
        }

        // Funkce pro automatický posun
        function autoPlay() {
            if (!isPaused) {
                index = (index + 1) % obrazky.length;
                zobrazObrazek(index);
            }
        }

        // Reset intervalu
        function resetInterval() {
            clearInterval(interval);
            interval = setInterval(autoPlay, 4000);
        }

        // Pauza při najetí myší
        slider.addEventListener('mouseenter', () => {
            isPaused = true;
        });

        slider.addEventListener('mouseleave', () => {
            isPaused = false;
        });

        // Spuštění slideru
        zobrazObrazek(index);
        interval = setInterval(autoPlay, 4000);
    }

    // ======= 3. PARALLAX EFEKT PRO BOXY =======
    let ticking = false;

    function updateParallax() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.blkt-kontejner-vstup');

        parallaxElements.forEach((el, index) => {
            // Různá rychlost pro každý box
            const speed = 0.5 + (index * 0.1);
            const yPos = -(scrolled * speed / 10);

            // Aplikujeme pouze pokud není v hover stavu
            if (!el.matches(':hover')) {
                el.style.transform = `translateY(${yPos}px)`;
            }
        });

        ticking = false;
    }

    function requestTick() {
        if (!ticking) {
            window.requestAnimationFrame(updateParallax);
            ticking = true;
        }
    }

    // Parallax pouze na větších obrazovkách
    if (window.innerWidth > 768) {
        window.addEventListener('scroll', requestTick);
    }

    // ======= 4. ANIMACE TEXTU PŘI NAČTENÍ =======
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
});