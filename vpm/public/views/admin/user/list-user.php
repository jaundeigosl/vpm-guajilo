<?php
require_once __DIR__ . '/../../../../app/init.php';

use App\middleware\AuthMiddleware;
use App\controllers\UserController;

header('Content-Type: text/html; charset=utf-8');
AuthMiddleware::requireRole(['admin']);

$controller = new UserController();

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;

$totalItems = $controller->getTotalCount();
$includeInactive = isset($_GET['show_inactive']) && $_GET['show_inactive'] == '1';
$users = $controller->getUsersWithRoleNames($currentPage, $perPage, $includeInactive);
?>

<h2>Lista de Usuarios</h2>
<a href="/views/admin/user/create-user.php" class="dynamic-link btn-crud">➕ Crear nuevo usuario</a>

<a href="/views/admin/user/list-user.php<?= $includeInactive ? '' : '?show_inactive=1' ?>"
   class="dynamic-link btn-crud">
    <?= $includeInactive ? 'Ver solo activos' : 'Ver también inactivos' ?>
</a>

<table class="table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Rol</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user->id ?></td>
            <td><?= htmlspecialchars("{$user->name} {$user->lastname}") ?></td>
            <td><?= htmlspecialchars($user->email) ?></td>
            <td><?= htmlspecialchars($user->role_name) ?></td>
            <td><?= $user->active ? 'Activo' : 'Inactivo' ?></td>
            <td>
                <a href="/views/admin/user/update-user.php?id=<?= $user->id ?>" class="dynamic-link">✏️ Editar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php
$baseUrl = $_SERVER['PHP_SELF'];
include __DIR__ . '/../../components/pagination.php';
?>
