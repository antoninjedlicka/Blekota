// intro.js – zobrazení textu s fade-in pouze při prvním vstupu

document.addEventListener('DOMContentLoaded', () => {
    const introAlreadyShown = sessionStorage.getItem('blkt_intro_shown');
    const loaderText = document.getElementById('blkt-loader-text');

    if (!loaderText) return;

    if (!introAlreadyShown) {
        loaderText.textContent = "Vítejte v blekotíně!";
        loaderText.classList.add('show'); // spustíme přechod

        // Text zůstane chvíli viditelný, pak ho odstraníme z DOM
        setTimeout(() => {
            loaderText.classList.remove('show');
            setTimeout(() => {
                loaderText.remove();
            }, 400); // musí odpovídat CSS transition
        }, 1500); // viditelnost textu 1.5s před odstraněním
    }

    sessionStorage.setItem('blkt_intro_shown', 'true');
});
