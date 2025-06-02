<?php
require_once __DIR__ . '/../../../app/init.php';

use App\middleware\AuthMiddleware;
use App\controllers\PriceController;

AuthMiddleware::requireRole(['admin']);

$controller = new PriceController();
$error = '';
$success = false;

$currentMonth = date('Y-m');

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $month = $_POST['month'] ?? '';
    $daysData = $_POST['days'] ?? [];

    if (!$month || !preg_match('/^\d{4}-\d{2}$/', $month) || empty($daysData)) {
        $error = 'Todos los campos son obligatorios.';
    } else {
        [$year, $monthNum] = explode('-', $month);

        foreach ($daysData as $day => $fields) {
            $controller->create([
                'day' => (int)$day,
                'month' => (int)$monthNum,
                'year' => (int)$year,
                'daily_hsc_price' => (float)($fields['hsc'] ?? 0),
                'daily_exchange_rate' => (float)($fields['exchange'] ?? 0)
            ]);
        }

        require __DIR__ . '/list-price.php';
        exit;
    }
}
?>

<h2>Precios Diarios <span class="separator">|</span> Nuevo registro</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="/views/price/create-price.php" class="form ajax-form">
    <div class="card">
        <h3>Información</h3>

        <div class="form-row">
            <div class="form-group">
                <label for="month">Mes</label>
                <input type="month" name="month" id="month" value="<?= $currentMonth ?>" required>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="calorific-table">
                <thead>
                <tr>
                    <th>Día</th>
                    <th>HSC Diario<br><small>USD/MMBtu</small></th>
                    <th>TC Diario Reuters<br><small>Pesos/USD</small></th>
                </tr>
                </thead>
                <tbody>
                <?php for ($i = 1; $i <= 31; $i++): ?>
                    <tr>
                        <td><strong><?= $i ?></strong></td>
                        <td><input type="number" name="days[<?= $i ?>][hsc]" step="0.01" min="0" value="0" required></td>
                        <td><input type="number" name="days[<?= $i ?>][exchange]" step="0.01" min="0" value="0" required></td>
                    </tr>
                <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <div class="form-actions">
            <button type="submit">Guardar</button>
            <a href="/views/price/list-price.php" class="dynamic-link cancel-btn">Cancelar</a>
        </div>
    </div>
</form>