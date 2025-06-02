<?php
require_once __DIR__ . '/../../../../app/init.php';

use App\controllers\RoleController;
use App\controllers\UserController;
use App\middleware\AuthMiddleware;

header('Content-Type: text/html; charset=utf-8');
AuthMiddleware::requireRole(['admin']);

$roleController = new RoleController();
$userController = new UserController();
$roles = $roleController->getActive();

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($name && $lastname && $email && $password && $role) {
        $result = $userController->adminRegister($name, $lastname, $email, $password, $role);
        if ($result['success']) {
            require __DIR__ . '/list-user.php';
            exit;
        } else {
            $error = $result['message'];
        }
    } else {
        $error = 'Todos los campos son obligatorios.';
    }
}
?>

<h2>Crear nuevo usuario</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form id="create-user-form" method="POST" action="/views/admin/user/create-user.php" class="form ajax-form">
    <label for="name">Nombre:</label>
    <input type="text" name="name" id="name" required>

    <label for="lastname">Apellido:</label>
    <input type="text" name="lastname" id="lastname" required>

    <label for="email">Correo electrónico:</label>
    <input type="email" name="email" id="email" required>

    <label for="password">Contraseña:</label>
    <input type="password" name="password" id="password" required>

    <label for="role">Rol:</label>
    <select name="role" id="role" required>
        <option value="">Seleccionar rol</option>
        <?php foreach ($roles as $r): ?>
            <option value="<?= htmlspecialchars($r->name) ?>"><?= htmlspecialchars($r->name) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Guardar</button>
    <a href="/views/admin/user/list-user.php" class="dynamic-link cancel-btn">Cancelar</a>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('create-user-form');

        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(form);

                fetch('create-user.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('main-content').innerHTML = html;
                    })
                    .catch(err => {
                        alert('Ocurrió un error al crear el usuario');
                        console.error(err);
                    });
            });
        }
    });
</script>