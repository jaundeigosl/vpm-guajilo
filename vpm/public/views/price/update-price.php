<?php
require_once __DIR__ . '/../../../app/init.php';

use App\controllers\PriceController;
use App\middleware\AuthMiddleware;

AuthMiddleware::requireRole('admin');

$controller = new PriceController();
$error = '';
$success = false;

// Obtener mes y año desde GET o POST
$monthParam = $_GET['month'] ?? $_POST['month'] ?? null;

if (!$monthParam || !preg_match('/^\d{4}-\d{2}$/', $monthParam)) {
    echo "<p>Error: Debes proporcionar un mes válido para editar.</p>";
    return;
}

[$year, $month] = explode('-', $monthParam);
$year = (int)$year;
$month = (int)$month;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $days = $_POST['days'] ?? [];

    if (empty($days)) {
        $error = 'No se proporcionaron datos para actualizar.';
    } else {
        // Borrar registros existentes del mes/año
        $all = $controller->getAll();
        foreach ($all as $entry) {
            if ($entry->year == $year && $entry->month == $month) {
                $controller->delete($entry->id);
            }
        }

        // Insertar nuevos registros
        foreach ($days as $day => $data) {
            $controller->create([
                'day' => (int)$day,
                'month' => $month,
                'year' => $year,
                'daily_hsc_price' => $data['hsc'] ?? 0,
                'daily_exchange_rate' => $data['exchange'] ?? 0,
            ]);
        }

        require __DIR__ . '/list-price.php';
        exit;
    }
}

// Cargar precios actuales
$allPrices = $controller->getAll();
$filtered = array_filter($allPrices, fn($p) => $p->year == $year && $p->month == $month);

$priceMap = [];
foreach ($filtered as $p) {
    $priceMap[$p->day] = [
        'hsc' => $p->daily_hsc_price,
        'exchange' => $p->daily_exchange_rate
    ];
}
?>

<h2>Precios <span class="separator">|</span> Editar precios</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="/views/price/update-price.php" class="form ajax-form">
    <input type="hidden" name="month" value="<?= htmlspecialchars($monthParam) ?>">

    <div class="card">
        <h3>Información</h3>

        <div class="form-row">
            <div class="form-group">
                <label>Mes</label>
                <input type="month" value="<?= $monthParam ?>" disabled>
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
                        <td>
                            <input type="number" name="days[<?= $i ?>][hsc]" min="0" step="0.01"
                                   value="<?= $priceMap[$i]['hsc'] ?? 0 ?>" required>
                        </td>
                        <td>
                            <input type="number" name="days[<?= $i ?>][exchange]" min="0" step="0.01"
                                   value="<?= $priceMap[$i]['exchange'] ?? 0 ?>" required>
                        </td>
                    </tr>
                <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <div class="form-actions">
            <button type="submit">Actualizar</button>
            <a href="/views/price/list-price.php" class="dynamic-link cancel-btn">Cancelar</a>
        </div>
    </div>
</form>
