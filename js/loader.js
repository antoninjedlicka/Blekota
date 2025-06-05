document.addEventListener("DOMContentLoaded", function () {
    const loader = document.getElementById('blkt-loader');

    function showLoader() {
        loader.classList.remove('blkt-loader--hidden', 'blkt-loader--invisible');
    }

    function hideLoader() {
        loader.classList.add('blkt-loader--hidden');
        // Počkej na konec transition (opacity), pak skryj úplně
        setTimeout(() => {
            loader.classList.add('blkt-loader--invisible');
        }, 400); // stejný jako CSS transition
    }

    // Loader při vstupu
    showLoader();
    window.addEventListener('load', () => {
        setTimeout(hideLoader, 500); // +0.5s vizuální brzda
    });

    // Loader při kliknutí na odkaz
    document.body.addEventListener('click', function (e) {
        const link = e.target.closest('a');
        if (
            link &&
            link.href &&
            !link.hasAttribute('target') &&
            link.origin === window.location.origin &&
            !link.href.startsWith('javascript:')
        ) {
            e.preventDefault();
            showLoader();
            setTimeout(() => {
                window.location.href = link.href;
            }, 500);
        }
    });
});
