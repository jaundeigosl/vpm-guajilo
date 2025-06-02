<?php
require_once __DIR__ . '/../../../../app/init.php';

use App\controllers\UserController;
use App\controllers\RoleController;
use App\middleware\AuthMiddleware;

AuthMiddleware::requireRole(['admin', 'cliente']);

global $currentUser;

$controller = new UserController();
$roleController = new RoleController();

$userId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$isAdmin = in_array('admin', $currentUser['roles']);

$user = $controller->getById($userId);
if (!$user) {
    echo "<p class='error'>Usuario no encontrado.</p>";
    exit;
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'] ?? '',
        'lastname' => $_POST['lastname'] ?? '',
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
    ];

    if ($isAdmin) {
        $data['role_id'] = $_POST['role_id'] ?? '';
        $data['active'] = isset($_POST['active']) ? 1 : 0;
    }

    $result = $controller->editUser($userId, $data);
    $success = $result['success'];
    $error = $result['message'];

    if ($success) {
        require __DIR__ . '/list-user.php';
        exit;
    }
}

// Obtener roles si es admin
$roles = $isAdmin ? $roleController->getActive() : [];
?>

<h2>Editar usuario</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form id="update-user-form" method="POST" action="/views/admin/user/update-user.php?id=<?= $userId ?>"
      class="form ajax-form">
    <label for="name">Nombre:</label>
    <input type="text" name="name" id="name" value="<?= htmlspecialchars($user->name) ?>" required>

    <label for="lastname">Apellido:</label>
    <input type="text" name="lastname" id="lastname" value="<?= htmlspecialchars($user->lastname) ?>" required>

    <label for="email">Correo electrónico:</label>
    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user->email) ?>" required>

    <label for="password">Contraseña (dejar en blanco para no cambiar):</label>
    <input type="password" name="password" id="password">

    <?php if ($isAdmin): ?>
        <label for="role_id">Rol:</label>
        <select name="role_id" id="role_id">
            <?php foreach ($roles as $role): ?>
                <option value="<?= $role->id ?>" <?= $role->id == $user->role_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($role->name) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>
            <input type="checkbox" name="active" value="1" <?= $user->active ? 'checked' : '' ?>>
            Cuenta activa
        </label>
    <?php endif; ?>

    <button type="submit">Actualizar</button>
    <a href="/views/admin/user/list-user.php" class="dynamic-link cancel-btn">Cancelar</a>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('update-user-form');

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
                        alert('Ocurrió un error al actualizar el usuario');
                        console.error(err);
                    });
            });
        }
    });
</script>