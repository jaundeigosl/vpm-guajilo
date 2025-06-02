<?php
require_once __DIR__ . '/../../../../app/init.php';

use App\controllers\ClientController;
use App\middleware\AuthMiddleware;

header('Content-Type: text/html; charset=utf-8');
AuthMiddleware::requireRole(['admin']);

$controller = new ClientController();

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;

$totalItems = $controller->getTotalCount();
$clients = $controller->getPaginated($currentPage, $perPage);

$baseUrl = $_SERVER['PHP_SELF'];
?>

<h2>Clientes</h2>

<a href="/views/admin/client/create-client.php" class="dynamic-link btn-crud">➕ Crear nuevo cliente</a>

<table class="table">
    <thead>
    <tr>
        <th>Nombre</th>
        <th>Alias</th>
        <th>RFC</th>
        <th>Correo</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($clients as $client): ?>
        <tr>
            <td><?= htmlspecialchars($client->name) ?></td>
            <td><?= htmlspecialchars($client->alias) ?></td>
            <td><?= htmlspecialchars($client->rfc) ?></td>
            <td><?= htmlspecialchars($client->email) ?></td>
            <td><?= $client->active ? 'Activo' : 'Inactivo' ?></td>
            <td>
                <a href="/views/admin/client/update-client.php?id=<?= $client->id ?>" class="dynamic-link">✏️ Editar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../../components/pagination.php'; ?>
