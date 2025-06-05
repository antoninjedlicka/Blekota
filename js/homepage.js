document.addEventListener('DOMContentLoaded', () => {
    const boxy = document.querySelectorAll('.blkt-kontejner-vstup');
    boxy.forEach((box, index) => {
        setTimeout(() => {
            box.classList.add('blkt-zobrazit');
        }, index * 200); // zpoždění 0.2s
    });
});

// Automatický posuv galerie (slider)
document.addEventListener('DOMContentLoaded', () => {
    const slider = document.querySelector('.blkt-slider');
    if (!slider) return;

    const obrazky = slider.querySelectorAll('img');
    let index = 0;

    function zobrazObrazek(novyIndex) {
        obrazky.forEach((img, i) => {
            img.classList.toggle('blkt-slider-aktivni', i === novyIndex);
        });
    }

    setInterval(() => {
        index = (index + 1) % obrazky.length;
        zobrazObrazek(index);
    }, 4000);

    zobrazObrazek(index); // první obrázek při načtení
});
