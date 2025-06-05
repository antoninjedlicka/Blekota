// Funkce pro animaci příspěvků
function blkt_animujPrispevky() {
    const kontejnery = document.querySelectorAll('.blkt-blog-kontejner');
    kontejnery.forEach((el, index) => {
        el.style.animationDelay = `${index * 0.5}s`;
    });
}

// Spustí se při načtení stránky
document.addEventListener('DOMContentLoaded', () => {
    blkt_animujPrispevky();
});

// A funkci si můžeš zavolat i z jiného JS kdykoli znovu:
// např. po AJAX načtení nových příspěvků:
// blkt_animujPrispevky();
