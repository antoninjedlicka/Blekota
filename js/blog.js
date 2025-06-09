// Funkce pro animaci příspěvků
function blkt_animujPrispevky() {
    const items = document.querySelectorAll('.blkt-masonry-item');
    items.forEach((el, index) => {
        // Nastavení zpoždění animace už je v CSS, ale můžeme přidat další efekty
        el.style.animationDelay = `${index * 0.1}s`;
    });
}

// Funkce pro dynamické přeuspořádání masonry layoutu
function blkt_uspsoradejMasonry() {
    const container = document.querySelector('.blkt-blog-masonry');
    const items = Array.from(container.querySelectorAll('.blkt-masonry-item'));

    // Na mobilech necháme výchozí pořadí
    if (window.innerWidth <= 768) {
        return;
    }

    // První položka zůstává vlevo nahoře (už je nastavena v CSS)
    // Ostatní položky můžeme případně přeuspořádat pro lepší vizuální efekt
}

// Funkce pro lazy loading obrázků
function blkt_nastavLazyLoading() {
    const obrazky = document.querySelectorAll('.blkt-masonry-obrazek img');

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                // Můžeme přidat lazy loading logiku zde
                img.classList.add('loaded');
                observer.unobserve(img);
            }
        });
    }, {
        rootMargin: '50px 0px',
        threshold: 0.01
    });

    obrazky.forEach(img => imageObserver.observe(img));
}

// Funkce pro parallax efekt při scrollování
function blkt_parallaxEfekt() {
    const items = document.querySelectorAll('.blkt-masonry-item');

    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;

        items.forEach((item, index) => {
            const speed = 0.05 * (index % 2 === 0 ? 1 : -1);
            const yPos = -(scrolled * speed);

            if (Math.abs(yPos) < 50) { // Omezení efektu
                item.style.transform = `translateY(${yPos}px)`;
            }
        });
    });
}

// Spustí se při načtení stránky
document.addEventListener('DOMContentLoaded', () => {
    blkt_animujPrispevky();
    blkt_uspsoradejMasonry();
    blkt_nastavLazyLoading();

    // Parallax efekt pouze na větších obrazovkách
    if (window.innerWidth > 768) {
        blkt_parallaxEfekt();
    }
});

// Při změně velikosti okna
window.addEventListener('resize', () => {
    blkt_uspsoradejMasonry();
});

// Export funkcí pro použití v jiných skriptech
window.blkt_blog = {
    animujPrispevky: blkt_animujPrispevky,
    uspsoradejMasonry: blkt_uspsoradejMasonry
};