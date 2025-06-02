document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.submenu-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const item = button.closest('.sidebar__item');
            item.classList.toggle('active');
        });
    });
});
