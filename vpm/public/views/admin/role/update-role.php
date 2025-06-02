<?php
require_once __DIR__ . '/../../../../app/init.php';

use App\controllers\RoleController;
use App\middleware\AuthMiddleware;

header('Content-Type: text/html; charset=utf-8');
AuthMiddleware::requireRole(['admin']);

$controller = new RoleController();

$id = (int) ($_GET['id'] ?? 0);
$role = $controller->getById($id);
$error = '';
$success = false;

if (!$role) {
    echo '<p class="error">Rol no encontrado.</p>';
    exit;
}

// Procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if ($name === '') {
        $error = 'El nombre del rol es obligatorio.';
    } else {
        $success = $controller->update($id, ['name' => $name]);

        if (!$success) {
            $error = 'Error al actualizar el rol.';
        }
    }

    if ($success) {
        require __DIR__ . '/list-role.php';
        exit;
    }
}
?>

<h2>Editar rol</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form id="update-role-form" method="POST" action="/views/admin/role/update-role.php?id=<?= $id ?>" class="form ajax-form">
    <label for="name">Nombre del rol:</label>
    <input type="text" name="name" id="name" value="<?= htmlspecialchars($role->name) ?>" required>

    <button type="submit">Actualizar</button>
    <a href="/views/admin/role/list-role.php" class="dynamic-link cancel-btn">Cancelar</a>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('update-role-form');

        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('main-content').innerHTML = html;
                    })
                    .catch(err => {
                        alert('Ocurrió un error al actualizar el rol');
                        console.error(err);
                    });
            });
        }
    });
</script>
