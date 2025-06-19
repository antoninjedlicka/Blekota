// loader.js - Univerzální loader pro celý web
document.addEventListener("DOMContentLoaded", function () {
    const loader = document.getElementById('blkt-loader');
    const progressBar = document.querySelector('.blkt-loader-progress-bar');
    let progress = 0;
    let progressInterval;
    let isNavigating = false; // Flag pro navigaci

    function showLoader(continueProgress = false) {
        loader.classList.remove('blkt-loader--hidden', 'blkt-loader--invisible');

        if (!continueProgress) {
            // Nová navigace - začni od začátku
            startProgress();
        } else {
            // Pokračuj kde jsi skončil
            continueProgress();
        }
    }

    function hideLoader() {
        // Pokud navigujeme pryč, neresetuj progress
        if (!isNavigating) {
            // Dokončíme progress
            if (progressBar) {
                progressBar.style.width = '100%';
            }

            setTimeout(() => {
                loader.classList.add('blkt-loader--hidden');
                // Počkej na konec transition (opacity), pak skryj úplně
                setTimeout(() => {
                    loader.classList.add('blkt-loader--invisible');
                    resetProgress();
                }, 500);
            }, 200);
        } else {
            // Při navigaci necháme loader viditelný
            loader.classList.remove('blkt-loader--hidden', 'blkt-loader--invisible');
        }
    }

    function startProgress() {
        progress = 0;
        if (progressBar) {
            progressBar.style.width = '0%';
        }

        clearInterval(progressInterval);

        // Simulace načítání
        progressInterval = setInterval(() => {
            progress += Math.random() * 30;
            if (progress > 90) {
                progress = 90; // Zastavíme na 90% dokud se stránka nenačte
                clearInterval(progressInterval);
            }
            if (progressBar) {
                progressBar.style.width = progress + '%';
            }
        }, 200);
    }

    function continueProgress() {
        // Pokračuj od aktuální pozice
        clearInterval(progressInterval);

        progressInterval = setInterval(() => {
            progress += Math.random() * 20;
            if (progress > 90) {
                progress = 90;
                clearInterval(progressInterval);
            }
            if (progressBar) {
                progressBar.style.width = progress + '%';
            }
        }, 200);
    }

    function resetProgress() {
        if (!isNavigating) {
            clearInterval(progressInterval);
            progress = 0;
            if (progressBar) {
                progressBar.style.width = '0%';
            }
        }
    }

    // Loader při vstupu - zkontroluj, jestli je progress v sessionStorage
    const savedProgress = sessionStorage.getItem('loaderProgress');
    if (savedProgress && parseFloat(savedProgress) > 0) {
        progress = parseFloat(savedProgress);
        if (progressBar) {
            progressBar.style.width = progress + '%';
        }
        showLoader(true); // Pokračuj v progressu
    } else {
        showLoader(false); // Začni od začátku
    }

    // Skrýt loader po načtení stránky
    window.addEventListener('load', () => {
        isNavigating = false;
        sessionStorage.removeItem('loaderProgress');
        setTimeout(hideLoader, 800);
    });

    // Loader při kliknutí na odkaz
    document.body.addEventListener('click', function (e) {
        const link = e.target.closest('a');
        if (
            link &&
            link.href &&
            !link.hasAttribute('target') &&
            !link.dataset.noLoader &&
            link.origin === window.location.origin &&
            !link.href.startsWith('javascript:') &&
            !link.href.includes('#')
        ) {
            e.preventDefault();
            isNavigating = true;

            // Ulož aktuální progress
            sessionStorage.setItem('loaderProgress', progress.toString());

            // Pokračuj v načítání
            continueProgress();

            setTimeout(() => {
                window.location.href = link.href;
            }, 100); // Kratší delay
        }
    });

    // Loader při odeslání formuláře
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form.dataset.noLoader) {
            isNavigating = true;
            sessionStorage.setItem('loaderProgress', progress.toString());
            continueProgress();
        }
    });

    // Loader při navigaci zpět/vpřed
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            isNavigating = false;
            hideLoader();
        }
    });

    // Při opuštění stránky ulož progress
    window.addEventListener('beforeunload', function() {
        if (isNavigating && progress > 0) {
            sessionStorage.setItem('loaderProgress', progress.toString());
        }
    });

    // Exponovat funkce pro případné ruční použití
    window.showPageLoader = () => showLoader(false);
    window.hidePageLoader = hideLoader;
});