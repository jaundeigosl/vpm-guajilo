<?php
require_once __DIR__ . '/../../../app/init.php';

use App\controllers\ProductServiceController;
use App\middleware\AuthMiddleware;

AuthMiddleware::requireRole(['admin']);

$controller = new ProductServiceController();

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'code' => $_POST['code'] ?? '',
        'description' => $_POST['description'] ?? '',
        'line' => $_POST['line'] ?? '',
        'stock' => $_POST['stock'] ?? 0,
        'output_unit' => $_POST['output_unit'] ?? '',
        'scheme_code' => $_POST['scheme_code'] ?? '',
        'sat_code' => $_POST['sat_code'] ?? '',
        'unit_code' => $_POST['unit_code'] ?? '',
        'alt_code' => $_POST['alt_code'] ?? ''
    ];

    $created = $controller->create($data);

    if ($created) {
        require __DIR__ . '/list-product-service.php';
        exit;
    } else {
        $error = 'No se pudo crear el producto o servicio.';
    }
}
?>

<h2>Productos y Servicios <span class="separator">|</span> Crear nuevo</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="/views/product_service/create-product-service.php" class="form ajax-form">
    <div class="card">
        <h3>Información del producto o servicio</h3>

        <div class="form-row">
            <div class="form-group">
                <label for="code">Código</label>
                <input type="text" name="code" id="code" required>
            </div>

            <div class="form-group">
                <label for="description">Descripción</label>
                <input type="text" name="description" id="description" required>
            </div>

            <div class="form-group">
                <label for="line">Línea</label>
                <input type="text" name="line" id="line">
            </div>

            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" name="stock" id="stock" min="0" step="1" value="0">
            </div>

            <div class="form-group">
                <label for="output_unit">Unidad de salida</label>
                <input type="text" name="output_unit" id="output_unit">
            </div>

            <div class="form-group">
                <label for="scheme_code">Código de esquema</label>
                <input type="text" name="scheme_code" id="scheme_code">
            </div>

            <div class="form-group">
                <label for="sat_code">Código SAT</label>
                <input type="text" name="sat_code" id="sat_code">
            </div>

            <div class="form-group">
                <label for="unit_code">Código de unidad</label>
                <input type="text" name="unit_code" id="unit_code">
            </div>

            <div class="form-group">
                <label for="alt_code">Código alterno</label>
                <input type="text" name="alt_code" id="alt_code">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit">Guardar</button>
            <a href="/views/product_service/list-product-service.php" class="dynamic-link cancel-btn">Cancelar</a>
        </div>
    </div>
</form>