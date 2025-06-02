<?php
require_once __DIR__ . '/../../../app/init.php';

use App\controllers\HolidayController;
use App\middleware\AuthMiddleware;

header('Content-Type: text/html; charset=utf-8');
AuthMiddleware::requireRole(['admin']);

$controller = new HolidayController();

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;

$totalItems = $controller->getTotalCount();
$holidays = $controller->getPaginated($currentPage, $perPage);

$baseUrl = $_SERVER['PHP_SELF'];
?>

<h2>Días Festivos</h2>

<a href="/views/holiday/create-holiday.php" class="dynamic-link btn-crud">➕ Crear nuevo día festivo</a>

<table class="table">
    <thead>
    <tr>
        <th>Fecha</th>
        <th>Nombre</th>
        <th>Acciones</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($holidays as $holiday): ?>
        <tr>
            <td><?= date('d-m-Y', strtotime($holiday->date)) ?></td>
            <td><?= htmlspecialchars($holiday->name) ?></td>
            <td>
                <a href="/views/holiday/update-holiday.php?id=<?= $holiday->id ?>" class="dynamic-link">✏️ Editar</a>
            </td>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>

<?php
include __DIR__ . '/../components/pagination.php';
?>