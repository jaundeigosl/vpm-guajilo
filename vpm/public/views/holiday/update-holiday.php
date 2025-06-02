<?php
require_once __DIR__ . '/../../../app/init.php';

use App\controllers\HolidayController;
use App\middleware\AuthMiddleware;

AuthMiddleware::requireRole('admin');

$controller = new HolidayController();
$error = '';
$success = false;

// Obtener el ID del festivo a editar
$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo "<p>Error: ID no válido.</p>";
    return;
}

$holiday = $controller->getById((int)$id);

if (!$holiday) {
    echo "<p>Error: Día festivo no encontrado.</p>";
    return;
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $date = $_POST['date'] ?? '';

    if ($name === '' || $date === '') {
        $error = 'Todos los campos son obligatorios.';
    } else {
        $updated = $controller->update((int)$id, [
            'name' => $name,
            'date' => $date
        ]);

        if ($updated) {
            require __DIR__ . '/list-holiday.php';
            exit;
        } else {
            $error = 'No se pudo actualizar el día festivo.';
        }
    }
}
?>

<h2>Días Festivos <span class="separator">|</span> Editar día festivo</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="/views/holiday/update-holiday.php" class="form ajax-form">
    <input type="hidden" name="id" value="<?= $holiday->id ?>">

    <div class="card">
        <h3>Información del día festivo</h3>

        <div class="form-row">
            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($holiday->name) ?>" required>
            </div>

            <div class="form-group">
                <label for="date">Fecha</label>
                <input type="date" name="date" id="date" value="<?= $holiday->date ?>" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit">Actualizar</button>
            <a href="/views/holiday/list-holiday.php" class="dynamic-link cancel-btn">Cancelar</a>
        </div>
    </div>
</form>