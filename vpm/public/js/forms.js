document.addEventListener('DOMContentLoaded', () => {
    // Delegación para formularios AJAX en main-content
    document.body.addEventListener('submit', function (e) {
        const form = e.target.closest('form.ajax-form');

        e.preventDefault();
        const formData = new FormData(form);
        const action = form.getAttribute('action');
    
        fetch(action, {
            method: 'POST',
            body: formData
        })
            .then(res => res.text())
            .then(html => {
                document.getElementById('main-content').innerHTML = html;
            })
            .catch(error => {
                alert('Error al procesar el formulario.');
                console.error(error);
            });
    });
});