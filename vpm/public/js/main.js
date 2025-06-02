document.addEventListener('DOMContentLoaded', () => {
    const mainContent = document.getElementById('main-content');

    function loadView(url) {
        fetch(url)
            .then(response => response.text())
            .then(html => {
                mainContent.innerHTML = html;

                if (typeof initDataTables === 'function') initDataTables();
                if (typeof initRoleTable === 'function') initRoleTable();
                if (typeof initUserForm === 'function') initUserForm();
            })
            .catch(error => {
                mainContent.innerHTML = '<p>Error al cargar la vista seleccionada.</p>';
                console.error('Error:', error);
            });
    }

    loadView('../../views/admin/graphics.php');

    document.body.addEventListener('click', function (e) {
        const link = e.target.closest('.dynamic-link');
        if (!link) return;

        e.preventDefault();
        const url = link.dataset.url || link.getAttribute('href');
        if (url) loadView(url);
    });
});