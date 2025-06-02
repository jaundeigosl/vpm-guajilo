<?php
require_once __DIR__ . '/../../../app/init.php';

use App\controllers\HolidayController;
use App\middleware\AuthMiddleware;

AuthMiddleware::requireRole(['admin']);

$controller = new HolidayController();

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $date = $_POST['date'] ?? '';

    if ($name === '' || $date === '') {
        $error = 'Todos los campos son obligatorios.';
    } else {
        $created = $controller->create([
            'name' => $name,
            'date' => $date
        ]);

        if ($created) {
            require __DIR__ . '/list-holiday.php';
            exit;
        } else {
            $error = 'No se pudo guardar el día festivo.';
        }
    }
}
?>

<h2>Días Festivos <span class="separator">|</span> Crear nuevo día festivo</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="/views/holiday/create-holiday.php" class="form ajax-form">
    <div class="card">
        <h3>Información del día festivo</h3>

        <div class="form-row">
            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" name="name" id="name" required>
            </div>

            <div class="form-group">
                <label for="date">Fecha</label>
                <input type="date" name="date" id="date" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit">Guardar</button>
            <a href="/views/holiday/list-holiday.php" class="dynamic-link cancel-btn">Cancelar</a>
        </div>
    </div>
</form>