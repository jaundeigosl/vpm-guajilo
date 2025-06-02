<?php
require_once __DIR__ . '/../../../app/init.php';

use App\controllers\BillingPeriodController;
use App\middleware\AuthMiddleware;

AuthMiddleware::requireRole(['admin']);

$controller = new BillingPeriodController();
$error = '';
$success = false;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $duration = $_POST['duration_days'] ?? '';
    $active = isset($_POST['active']) ? 1 : 0;

    if ($name === '' || $duration === '') {
        $error = 'El nombre y la duración son obligatorios.';
    } else {
        $created = $controller->create([
            'name' => $name,
            'start_date' => $startDate !== '' ? $startDate : null,
            'end_date' => $endDate !== '' ? $endDate : null,
            'duration_days' => (int)$duration,
            'active' => $active
        ]);

        if ($created) {
            require __DIR__ . '/list-billing-period.php';
            exit;
        } else {
            $error = 'No se pudo guardar el periodo de facturación.';
        }
    }
}
?>

<h2>Periodos de Facturación <span class="separator">|</span> Crear nuevo periodo</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="/views/billing_period/create-billing-period.php" class="form ajax-form">
    <div class="card">
        <h3>Información del periodo</h3>

        <div class="form-row">
            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" name="name" id="name" required>
            </div>

<!--            <div class="form-group">-->
<!--                <label for="start_date">Fecha de inicio (opcional)</label>-->
<!--                <input type="date" name="start_date" id="start_date">-->
<!--            </div>-->
<!---->
<!--            <div class="form-group">-->
<!--                <label for="end_date">Fecha de fin (opcional)</label>-->
<!--                <input type="date" name="end_date" id="end_date">-->
<!--            </div>-->

            <div class="form-group">
                <label for="duration_days">Duración en días</label>
                <input type="number" name="duration_days" id="duration_days" min="1" required>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="active" checked>
                    Activo
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit">Guardar</button>
            <a href="/views/billing_period/list-billing-period.php" class="dynamic-link cancel-btn">Cancelar</a>
        </div>
    </div>
</form>