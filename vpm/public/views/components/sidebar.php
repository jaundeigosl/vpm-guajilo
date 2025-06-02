<?php
global $currentUser;
$roles = $currentUser['roles'] ?? [];

$menuItems = [
    'admin' => [
        'Dashboard' => '/views/admin/graphics.php',
        'Resumen|Consumos' => '/views/admin/summary.php',
        'Facturación|Cobranza' => '/views/providers.php',
        'Importar consumo' => '/views/imports/import.php',
        'Clientes' => '/views/admin/client/list-client.php',
        'Poder calórico' => '/views/calorific_value/list-calorific-value.php',
        'Precios' => '/views/price/list-price.php',
        'Días Hábiles' => '/views/holiday/list-holiday.php',
        'Email' => '/views/admin/client/send-client-email.php',
        'Usuarios' => '/views/downloads.php',
        'Productos|Servicios' => '/views/product_service/list-product-service.php',
        'Configuración' => '/views/billing_period/list-billing-period.php',
    ],
    'cliente' => [
        'Mi Perfil' => '/views/profile.php',
        'Mis Órdenes' => '/views/orders.php',
    ],
];

$allowed = [];
foreach ($roles as $role) {
    if (isset($menuItems[$role])) {
        $allowed = array_merge($allowed, $menuItems[$role]);
    }
}
?>

<aside class="sidebar">
    <!--    <button class="sidebar__close-btn" aria-label="Cerrar menú">✕</button>-->

    <nav class="sidebar__nav">
        <?php if (!empty($allowed)): ?>
            <ul class="sidebar__list">
                <?php foreach ($allowed as $label => $href): ?>
                    <?php if ($label === 'Facturación|Cobranza'): ?>
                        <li class="sidebar__item has-submenu">
                            <button class="submenu-toggle">
                                <span>Facturación & Cobranza</span>
                                <span class="arrow">▸</span>
                            </button>
                            <ul class="submenu">
                                <li><a href="/views/facturacion.php" class="dynamic-link">Resumen</a></li>
                                <li><a href="/views/cobranza.php" class="dynamic-link">Pagos</a></li>
                                <li><a href="/views/cobranza.php" class="dynamic-link">Fac Manual</a></li>
                                <li><a href="/views/cobranza.php" class="dynamic-link">NC-USD</a></li>
                                <li><a href="/views/cobranza.php" class="dynamic-link">NC-MXN</a></li>
                                <li><a href="/views/cobranza.php" class="dynamic-link">FAC-USD</a></li>
                                <li><a href="/views/cobranza.php" class="dynamic-link">FAC-MXN</a></li>
                                <li><a href="/views/cobranza.php" class="dynamic-link">CxC-USD</a></li>
                                <li><a href="/views/cobranza.php" class="dynamic-link">CxC-MXN</a></li>
                            </ul>
                        </li>
                    <?php elseif (strtolower($label) === 'usuarios'): ?>
                        <li class="sidebar__item has-submenu">
                            <button class="submenu-toggle">
                                <span>Usuarios</span>
                                <span class="arrow">▸</span>
                            </button>
                            <ul class="submenu">
                                <li><a href="../admin/user/list-user.php" class="dynamic-link">Lista de Usuarios</a>
                                </li>
                                <li><a href="../admin/role/list-role.php" class="dynamic-link">Roles</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="sidebar__item">
                            <a href="<?= htmlspecialchars($href) ?>" class="dynamic-link">
                                <span><?= htmlspecialchars($label) ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="sidebar__empty">No hay secciones disponibles.</p>
        <?php endif; ?>
    </nav>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.submenu-toggle').forEach(button => {
            button.addEventListener('click', () => {
                const item = button.closest('.sidebar__item');
                item.classList.toggle('active');
            });
        });
    });
</script>
