<?php
require_once __DIR__ . '/../../../app/init.php';

use App\middleware\AuthMiddleware;
use App\controllers\BillingPeriodController;

header('Content-Type: text/html; charset=utf-8');
AuthMiddleware::requireRole(['admin']);

$controller = new BillingPeriodController();

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;

$includeInactive = isset($_GET['show_inactive']) && $_GET['show_inactive'] == '1';
$totalItems = $controller->getTotalCount($includeInactive);
$billingPeriods = $controller->getPaginated($currentPage, $perPage, $includeInactive);

$baseUrl = $_SERVER['PHP_SELF'];
?>

<h2>Periodos de Facturación</h2>

<a href="/views/billing_period/create-billing-period.php" class="dynamic-link btn-crud">➕ Crear nuevo período</a>

<a href="/views/billing_period/list-billing-period.php<?= $includeInactive ? '' : '?show_inactive=1' ?>"
   class="dynamic-link btn-crud">
    <?= $includeInactive ? 'Ver solo activos' : 'Ver también inactivos' ?>
</a>

<table class="table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Duración (días)</th>
<!--        <th>Inicio</th>-->
<!--        <th>Fin</th>-->
        <th>Estado</th>
        <th>Acciones</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($billingPeriods as $period): ?>
        <tr>
            <td><?= $period->id ?></td>
            <td><?= htmlspecialchars($period->name) ?></td>
            <td><?= htmlspecialchars($period->duration_days) ?></td>
<!--            <td>--><?php //= htmlspecialchars($period->start_date) ?><!--</td>-->
<!--            <td>--><?php //= htmlspecialchars($period->end_date) ?><!--</td>-->
            <td><?= $period->active ? 'Activo' : 'Inactivo' ?></td>
            <td>
                <a href="/views/billing_period/update-billing-period.php?id=<?= $period->id ?>" class="dynamic-link">✏️ Editar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php
include __DIR__ . '/../components/pagination.php';
?>