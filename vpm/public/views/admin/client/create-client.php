<?php
require_once __DIR__ . '/../../../../app/init.php';
require_once __DIR__ . '/../../../../app/helpers/form_helpers.php';

use App\controllers\ClientController;
use App\middleware\AuthMiddleware;

header('Content-Type: text/html; charset=utf-8');
AuthMiddleware::requireRole(['admin']);

$controller = new ClientController();
$catalogs = $controller->getAllCatalogs();

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $data['apply_rate_1_025'] = isset($data['apply_rate_1_025']) ? 1 : 0;
    $data['active'] = isset($data['active']) ? 1 : 0;

    $created = $controller->create($data);

    if ($created) {
        require __DIR__ . '/list-client.php';
        exit;
    } else {
        $error = 'No se pudo guardar el cliente.';
    }
}
?>

<h2>Clientes <span class="separator">|</span> Nuevo cliente</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="/views/admin/client/create-client.php" class="form ajax-form">
    <div class="form-columns">
        <!-- Datos Generales -->
        <div class="form-column">
            <h3>Datos generales</h3>
            <?= formInput('name', 'Nombre', ) ?>
            <?= formInput('alias', 'Alias') ?>
            <?= formInput('number', 'Número') ?>
            <?= formInput('email', 'Email', false, 'email') ?>
            <?= formInput('rfc', 'RFC') ?>
            <?= formInput('business_name', 'Razón Social') ?>
            <?= formInput('tax_address', 'Domicilio Fiscal') ?>
            <?= formInput('contact_data', 'Datos de Contacto') ?>
            <?= formSelect('region_id', 'Región', $catalogs['regions'] ?? []) ?>
            <?= formSelect('sector_id', 'Sector Productivo', $catalogs['sectors'] ?? []) ?>
        </div>

        <!-- Facturación -->
        <div class="form-column">
            <h3>Facturación</h3>
            <?= formInput('gn_molecule_delivery_perm', 'Permiso entrega GN') ?>
            <?= formInput('gn_service_description', 'Descripción servicio GN') ?>
            <?= formInput('hsc_rate', 'Tarifa HSC', false, 'number') ?>
            <?= formCheckbox('apply_rate_1_025', 'Aplicar tarifa 1.025') ?>
            <?= formInput('fuel_over_hsc', 'FUEL sobre HSC', false, 'number') ?>
            <?= formInput('gnc_service_rate', 'Tarifa Servicio GNC', false, 'number') ?>
            <?= formInput('transport_bf_rate', 'Tarifa Transporte BF', false, 'number') ?>
            <?= formInput('transport_bi_rate', 'Tarifa Transporte BI', false, 'number') ?>
            <?= formSelect('currency_molecule_id', 'Moneda Molécula', $catalogs['currency_molecule'] ?? []) ?>
            <?= formSelect('currency_service_id', 'Moneda Servicio', $catalogs['currency_service'] ?? []) ?>
            <?= formSelect('molecule_unit_id', 'Unidad Molécula', $catalogs['molecule_unit'] ?? []) ?>
            <?= formSelect('service_unit_id', 'Unidad Servicio', $catalogs['service_unit'] ?? []) ?>
            <?= formSelect('billing_unit_id', 'Unidad Facturación', $catalogs['billing_unit'] ?? []) ?>
            <?= formSelect('billing_period_id', 'Período de Facturación', $catalogs['billing_period'] ?? []) ?>
            <?= formSelect('gas_cfdi_use_id', 'Uso CFDI Gas', $catalogs['gas_cfdi_use'] ?? []) ?>
            <?= formSelect('service_cfdi_use_id', 'Uso CFDI Servicio', $catalogs['service_cfdi_use'] ?? []) ?>
        </div>
    </div>

    <div class="form-actions">
        <?= formCheckbox('active', 'Activo') ?>
        <button type="submit">Guardar</button>
        <a href="/views/admin/client/list-client.php" class="dynamic-link cancel-btn">Cancelar</a>
    </div>
</form>