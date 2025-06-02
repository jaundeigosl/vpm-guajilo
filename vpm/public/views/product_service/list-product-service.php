<?php
require_once __DIR__ . '/../../../app/init.php';

use App\controllers\ProductServiceController;
use App\middleware\AuthMiddleware;

header('Content-Type: text/html; charset=utf-8');
AuthMiddleware::requireRole(['admin']);

$controller = new ProductServiceController();

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;

$totalItems = $controller->getTotalCount();
$items = $controller->getPaginated($currentPage, $perPage);

$baseUrl = $_SERVER['PHP_SELF'];
?>

    <h2>Productos y Servicios</h2>

    <a href="/views/product_service/create-product-service.php" class="dynamic-link btn-crud">➕ Nuevo Producto o Servicio</a>

    <table class="table">
        <thead>
        <tr>
            <th>Clave</th>
            <th>Descripción</th>
            <th>Línea</th>
            <th>Existencias</th>
            <th>Unidad Salida</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item->code) ?></td>
                <td><?= htmlspecialchars($item->description) ?></td>
                <td><?= htmlspecialchars($item->line) ?></td>
                <td><?= htmlspecialchars($item->stock) ?></td>
                <td><?= htmlspecialchars($item->output_unit) ?></td>
                <td>
                    <a href="/views/product_service/update-product-service.php?id=<?= $item->id ?>" class="dynamic-link">✏️ Editar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php include __DIR__ . '/../components/pagination.php'; ?>