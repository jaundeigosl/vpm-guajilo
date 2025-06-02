<?php
$currentUser = $currentUser ?? [];
$username = htmlspecialchars($currentUser['name'] ?? 'name');
?>

<div class="header__usermenu">
    <button class="header__menu-toggle" aria-haspopup="true" aria-expanded="false">
        👤 <?= $username ?> ▾
    </button>
    <ul class="usermenu__dropdown">
        <li><a href="../user/profile.php" class="dynamic-link">👤 Perfil</a></li>
        <li><a href="../../auth/Logout.php">🚪 Cerrar sesión</a></li>
    </ul>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const menuBtn = document.querySelector('.header__menu-toggle');
        const userMenu = document.querySelector('.header__usermenu');

        menuBtn.addEventListener('click', () => {
            userMenu.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (!userMenu.contains(e.target)) {
                userMenu.classList.remove('active');
            }
        });
    });
</script>
