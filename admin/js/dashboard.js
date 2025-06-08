// admin/js/dashboard.js
function initDashboardSection() {
    console.log('Dashboard section loaded');

    // Načtení statistik
    loadDashboardStats();
}

function loadDashboardStats() {
    const stats = document.querySelector('.dashboard-stats');
    if (!stats) return;

    // Simulace načítání dat - nahraďte skutečným AJAX voláním
    setTimeout(() => {
        const items = stats.querySelectorAll('li');
        if (items[0]) items[0].innerHTML = '<strong>Počet uživatelů:</strong> Načítám...';
        if (items[1]) items[1].innerHTML = '<strong>Počet příspěvků:</strong> Načítám...';
        if (items[2]) items[2].innerHTML = '<strong>Aktuální téma webu:</strong> Načítám...';

        // Zde by mělo být skutečné načtení dat z API
        fetch('action/get_dashboard_stats.php')
            .then(r => r.json())
            .then(data => {
                if (items[0]) items[0].innerHTML = `<strong>Počet uživatelů:</strong> ${data.users || 0}`;
                if (items[1]) items[1].innerHTML = `<strong>Počet příspěvků:</strong> ${data.posts || 0}`;
                if (items[2]) items[2].innerHTML = `<strong>Aktuální téma webu:</strong> ${data.theme || 'Výchozí'}`;
            })
            .catch(err => {
                console.error('Chyba při načítání statistik:', err);
                stats.innerHTML = '<li>Nepodařilo se načíst statistiky.</li>';
            });
    }, 500);
}

// Spustit inicializaci
initDashboardSection();