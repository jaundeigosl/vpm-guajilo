<?php
require_once __DIR__ . '/../../../app/init.php';

use App\controllers\CalorificValueController;
use App\controllers\RegionController;
use App\middleware\AuthMiddleware;

AuthMiddleware::requireRole('admin');

$regionController = new RegionController();
$calorificController = new CalorificValueController();

$regions = $regionController->getAll();
$error = '';
$success = false;

// Obtener valores enviados o por GET para precargar
$regionId = $_GET['region_id'] ?? $_POST['region_id'] ?? null;
$month = $_GET['month'] ?? $_POST['month'] ?? null;

if (!$regionId || !$month || !preg_match('/^\d{4}-\d{2}$/', $month)) {
    echo "<p>Error: Faltan parámetros válidos para actualizar.</p>";
    return;
}

[$year, $monthNum] = explode('-', $month);
$year = (int)$year;
$monthNum = (int)$monthNum;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $daysData = $_POST['days'] ?? [];

    if (empty($daysData)) {
        $error = 'No se enviaron valores para actualizar.';
    } else {
        // Eliminar datos anteriores del mes/región
        $existing = $calorificController->getByRegion($regionId);
        foreach ($existing as $entry) {
            if ($entry->month == $monthNum && $entry->year == $year) {
                $calorificController->delete($entry->id);
            }
        }

        // Insertar nuevos valores
        foreach ($daysData as $day => $value) {
            $calorificController->create([
                'day' => (int)$day,
                'month' => $monthNum,
                'year' => $year,
                'region_id' => $regionId,
                'calorific_value' => $value
            ]);
        }

        require __DIR__ . '/list-calorific-value.php';
        exit;
    }
}

// Obtener valores actuales
$values = $calorificController->getByRegion($regionId);
$monthlyValues = array_filter($values, fn($v) => $v->month == $monthNum && $v->year == $year);
$dayMap = [];
foreach ($monthlyValues as $val) {
    $dayMap[$val->day] = $val->calorific_value;
}
?>

<h2>Poder Calórico <span class="separator">|</span> Editar poder calórico</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="/views/calorific_value/update-calorific-value.php" class="form ajax-form">
    <input type="hidden" name="region_id" value="<?= $regionId ?>">
    <input type="hidden" name="month" value="<?= htmlspecialchars($month) ?>">

    <div class="card">
        <h3>Información</h3>

        <div class="form-row">
            <div class="form-group">
                <label>Región</label>
                <input type="text"
                       value="<?= htmlspecialchars(array_values(array_filter($regions, fn($r) => $r->id == $regionId))[0]->name ?? '') ?>"
                       disabled>
            </div>

            <div class="form-group">
                <label>Mes</label>
                <input type="month" value="<?= $month ?>" disabled>
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
                            <input type="number" name="days[<?= $i ?>]" min="0" step="0.01"
                                   value="<?= $dayMap[$i] ?? 0 ?>" required>
                        </td>
                    </tr>
                <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <div class="form-actions">
            <button type="submit">Actualizar</button>
            <a href="/views/calorific_value/list-calorific-value.php" class="dynamic-link cancel-btn">Cancelar</a>
        </div>
    </div>
</form>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Delegación para formularios AJAX en main-content
        document.body.addEventListener('submit', function (e) {
            const form = e.target.closest('form.ajax-form');

            if (!form) return; // ⛔ Evita errores si no hay un formulario válido

            e.preventDefault();

            const formData = new FormData(form);
            const action = form.getAttribute('action');

            fetch(action, {
                method: 'POST',
                body: formData
            })
                .then(res => res.text())
                .then(html => {
                    document.getElementById('main-content').innerHTML = html;
                })
                .catch(error => {
                    alert('Error al procesar el formulario.');
                    console.error(error);
                });
        });
    });
</script>