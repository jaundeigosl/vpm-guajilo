<?php
require_once __DIR__ . '/../../../app/init.php';

use App\controllers\ProductServiceController;
use App\middleware\AuthMiddleware;

AuthMiddleware::requireRole(['admin']);

$controller = new ProductServiceController();
$error = '';
$success = false;

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    echo "<p class='error'>ID inválido</p>";
    return;
}

$product = $controller->getById((int)$id);
if (!$product) {
    echo "<p class='error'>Producto o servicio no encontrado.</p>";
    return;
}

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

    $success = $controller->update((int)$id, $data);
    if ($success) {
        require __DIR__ . '/list-product-service.php';
        exit;
    } else {
        $error = 'Error al actualizar el producto o servicio.';
    }
}
?>

<h2>Productos / Servicios <span class="separator">|</span> Editar</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="/views/product_service/update-product-service.php" class="form ajax-form">
    <input type="hidden" name="id" value="<?= $product->id ?>">

    <div class="card">
        <h3>Información</h3>
        <div class="form-row">
            <div class="form-group">
                <label for="code">Código</label>
                <input type="text" name="code" id="code" value="<?= htmlspecialchars($product->code) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Descripción</label>
                <input type="text" name="description" id="description" value="<?= htmlspecialchars($product->description) ?>" required>
            </div>
            <div class="form-group">
                <label for="line">Línea</label>
                <input type="text" name="line" id="line" value="<?= htmlspecialchars($product->line) ?>">
            </div>
            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" name="stock" id="stock" min="0" value="<?= htmlspecialchars($product->stock) ?>">
            </div>
            <div class="form-group">
                <label for="output_unit">Unidad de salida</label>
                <input type="text" name="output_unit" id="output_unit" value="<?= htmlspecialchars($product->output_unit) ?>">
            </div>
            <div class="form-group">
                <label for="scheme_code">Código de esquema</label>
                <input type="text" name="scheme_code" id="scheme_code" value="<?= htmlspecialchars($product->scheme_code) ?>">
            </div>
            <div class="form-group">
                <label for="sat_code">Código SAT</label>
                <input type="text" name="sat_code" id="sat_code" value="<?= htmlspecialchars($product->sat_code) ?>">
            </div>
            <div class="form-group">
                <label for="unit_code">Código de unidad</label>
                <input type="text" name="unit_code" id="unit_code" value="<?= htmlspecialchars($product->unit_code) ?>">
            </div>
            <div class="form-group">
                <label for="alt_code">Código alterno</label>
                <input type="text" name="alt_code" id="alt_code" value="<?= htmlspecialchars($product->alt_code) ?>">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit">Actualizar</button>
            <a href="/views/product_service/list-product-service.php" class="dynamic-link cancel-btn">Cancelar</a>
        </div>
    </div>
</form>