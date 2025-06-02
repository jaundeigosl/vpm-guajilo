<?php
require_once __DIR__ . '/../../../../app/init.php';

use App\controllers\RoleController;
use App\middleware\AuthMiddleware;

header('Content-Type: text/html; charset=utf-8');
AuthMiddleware::requireRole(['admin']);

$controller = new RoleController();

$success = false;
$error = '';

// Procesar el formulario si viene por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if ($name === '') {
        $error = 'El nombre del rol es obligatorio.';
    } else {
        $success = $controller->create(['name' => $name]);
        if (!$success) {
            $error = 'Error al crear el rol. Verifica los datos.';
        }
    }

    // Si fue exitoso, devolvemos la lista actualizada
    if ($success) {
        require __DIR__ . '/list-role.php';
        exit;
    }
}
?>

<h2>Crear nuevo rol</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form id="create-role-form" method="POST" action="/views/admin/role/create-role.php" class="form ajax-form">
    <label for="name">Nombre del rol:</label>
    <input type="text" name="name" id="name" required>

    <button type="submit">Guardar</button>
    <a href="list-role.php" class="dynamic-link cancel-btn">Cancelar</a>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('create-role-form');

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
                        alert('Ocurrió un error al guardar el rol');
                        console.error(err);
                    });
            });
        }
    });
</script>
