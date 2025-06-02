<?php
require_once __DIR__ . '/../../../app/init.php';

use App\middleware\AuthMiddleware;
use App\controllers\UserController;

header('Content-Type: text/html; charset=utf-8');
AuthMiddleware::requireAuth();

global $currentUser;
$controller = new UserController();

$user = $controller->getById((int)$currentUser['userId']);

$success = false;
$error = '';
$message = '';

// Procesar si viene por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'] ?? '',
        'lastname' => $_POST['lastname'] ?? '',
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
    ];

    $result = $controller->editUser((int)$currentUser['userId'], $data);

    if ($result['success']) {
        $message = $result['message'];
        $user = $controller->getById((int)$currentUser['userId']);
    } else {
        $error = $result['message'];
    }
}
?>

<h2>Mi perfil</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php elseif ($message): ?>
    <p class="success"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form id="profile-form" method="POST" action="/views/user/profile.php" class="form ajax-form">
    <label for="name">Nombre:</label>
    <input type="text" name="name" id="name" value="<?= htmlspecialchars($user->name ?? '') ?>" required>

    <label for="lastname">Apellido:</label>
    <input type="text" name="lastname" id="lastname" value="<?= htmlspecialchars($user->lastname ?? '') ?>" required>

    <label for="email">Correo:</label>
    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user->email ?? '') ?>" required>

    <label for="password">Nueva contraseña (opcional):</label>
    <input type="password" name="password" id="password">

    <button type="submit">Actualizar</button>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('profile-form');

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
                        alert('Error al actualizar el perfil');
                        console.error(err);
                    });
            });
        }
    });
</script>