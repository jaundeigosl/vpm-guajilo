<?php
require_once __DIR__ . '/../../../app/init.php';

use App\controllers\CalorificValueController;
use App\controllers\RegionController;
use App\middleware\AuthMiddleware;

AuthMiddleware::requireRole('admin');

$regionController = new RegionController();
$calorificValueController = new CalorificValueController();
$regions = $regionController->getAll();

$error = '';
$success = false;

// Si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $regionId = $_POST['region_id'] ?? '';
    $monthInput = $_POST['month'] ?? '';
    $daysData = $_POST['days'] ?? [];

    [$year, $month] = explode('-', $monthInput);

    if (!$regionId || !$month || !$year || empty($daysData)) {
        $error = 'Todos los campos son obligatorios.';
    } else {
        $result = $calorificValueController->createForMonth($regionId, $year, $month, $daysData);

        if ($result) {
            require __DIR__ . '/list-calorific-value.php';
            exit;
        } else {
            $error = 'No se pudo guardar el poder calórico.';
        }
    }
}

// Fecha actual por defecto
$currentMonth = date('m');
$currentYear = date('Y');
?>

<h2>Poder Calórico <span class="separator">|</span> Nuevo poder calórico</h2>

<div class="card">
    <h3>Información</h3>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="/views/calorific_value/create-calorific-value.php" class="form ajax-form">
        <div class="form-row">
            <div class="form-group">
                <label for="region_id">Región</label>
                <select name="region_id" id="region_id" required>
                    <option value="">Seleccionar región</option>
                    <?php foreach ($regions as $region): ?>
                        <option value="<?= $region->id ?>"><?= htmlspecialchars($region->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="month">Mes</label>
                <input type="month" name="month" id="month" value="<?= $currentYear ?>-<?= $currentMonth ?>" required>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="calorific-table">
                <thead>
                <tr>
                    <th>Día</th>
                    <th>Poder Calórico</th>
                </tr>
                </thead>
                <tbody>
                <?php for ($i = 1; $i <= 31; $i++): ?>
                    <tr>
                        <td><strong><?= $i ?></strong></td>
                        <td>
                            <input type="number" name="days[<?= $i ?>]" min="0" step="0.01" value="0" required>
                        </td>
                    </tr>
                <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <div class="form-actions">
            <button type="submit">Guardar</button>
            <a href="/views/calorific_value/list-calorific-value.php" class="dynamic-link cancel-btn">Cancelar</a>
        </div>
    </form>
</div>
