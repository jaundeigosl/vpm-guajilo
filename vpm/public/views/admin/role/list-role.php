<?php
require_once __DIR__ . '/../../../../app/init.php';

use App\controllers\RoleController;
use App\middleware\AuthMiddleware;

AuthMiddleware::requireRole(['admin']);

$controller = new RoleController();

// Paginación
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;

$roles = $controller->getPaginated($currentPage, $perPage);
$totalItems = $controller->getTotalCount();

?>

<h2>Lista de Roles</h2>

<a href="/views/admin/role/create-role.php" class="dynamic-link">➕ Crear nuevo rol</a>

<table class="table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Acciones</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($roles as $role): ?>
        <tr>
            <td><?= $role->id ?></td>
            <td><?= $role->name ?></td>
            <td>
                <a href="/views/admin/role/update-role.php?id=<?= $role->id ?>" class="dynamic-link">✏️ Editar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php
$baseUrl = $_SERVER['PHP_SELF'];
include __DIR__ . '/../../components/pagination.php';
?>
