<?php
require_once __DIR__ . '/../../../../app/init.php';
require_once __DIR__ . '/../../../../app/helpers/form_helpers.php';

use App\controllers\ClientController;
use App\middleware\AuthMiddleware;

header('Content-Type: text/html; charset=utf-8');
AuthMiddleware::requireRole(['admin']);

$controller = new ClientController();
$catalogs = $controller->getAllCatalogs();

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<p class='error'>ID no proporcionado.</p>";
    return;
}

$client = $controller->getById($id);
if (!$client) {
    echo "<p class='error'>Cliente no encontrado.</p>";
    return;
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $data['apply_rate_1_025'] = isset($data['apply_rate_1_025']) ? 1 : 0;
    $data['active'] = isset($data['active']) ? 1 : 0;

    $updated = $controller->update($id, $data);

    if ($updated) {
        require __DIR__ . '/list-client.php';
        exit;
    } else {
        $error = 'No se pudo actualizar el cliente.';
    }
}
?>

<h2>Clientes <span class="separator">|</span> Editar cliente</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="/views/admin/client/update-client.php?id=<?= $id ?>" class="form ajax-form">
    <div class="form-columns">
        <div class="form-column">
            <h3>Datos generales</h3>
            <?= formInput('name', 'Nombre', $client->name) ?>
            <?= formInput('alias', 'Alias', $client->alias) ?>
            <?= formInput('number', 'Número', $client->number) ?>
            <?= formInput('email', 'Email', $client->email, 'email') ?>
            <?= formInput('rfc', 'RFC', $client->rfc) ?>
            <?= formInput('business_name', 'Razón Social', $client->business_name) ?>
            <?= formInput('tax_address', 'Domicilio Fiscal', $client->tax_address) ?>
            <?= formInput('contact_data', 'Datos de Contacto', $client->contact_data) ?>
            <?= formSelect('region_id', 'Región', $catalogs['regions'], $client->region_id) ?>
            <?= formSelect('sector_id', 'Sector Productivo', $catalogs['sectors'], $client->sector_id) ?>
        </div>

        <div class="form-column">
            <h3>Facturación</h3>
            <?= formInput('gn_molecule_delivery_perm', 'Permiso entrega GN', $client->gn_molecule_delivery_perm) ?>
            <?= formInput('gn_service_description', 'Descripción servicio GN', $client->gn_service_description) ?>
            <?= formInput('hsc_rate', 'Tarifa HSC', $client->hsc_rate, 'number') ?>
            <?= formCheckbox('apply_rate_1_025', 'Aplicar tarifa 1.025', $client->apply_rate_1_025) ?>
            <?= formInput('fuel_over_hsc', 'FUEL sobre HSC', $client->fuel_over_hsc, 'number') ?>
            <?= formInput('gnc_service_rate', 'Tarifa Servicio GNC', $client->gnc_service_rate, 'number') ?>
            <?= formInput('transport_bf_rate', 'Tarifa Transporte BF', $client->transport_bf_rate, 'number') ?>
            <?= formInput('transport_bi_rate', 'Tarifa Transporte BI', $client->transport_bi_rate, 'number') ?>

            <?= formSelect('currency_molecule_id', 'Moneda Molécula', $catalogs['currency_molecule'], $client->currency_molecule_id) ?>
            <?= formSelect('currency_service_id', 'Moneda Servicio', $catalogs['currency_service'], $client->currency_service_id) ?>
            <?= formSelect('molecule_unit_id', 'Unidad Molécula', $catalogs['molecule_unit'], $client->molecule_unit_id) ?>
            <?= formSelect('service_unit_id', 'Unidad Servicio', $catalogs['service_unit'], $client->service_unit_id) ?>
            <?= formSelect('billing_unit_id', 'Unidad Facturación', $catalogs['billing_unit'], $client->billing_unit_id) ?>
            <?= formSelect('billing_period_id', 'Período de Facturación', $catalogs['billing_period'], $client->billing_period_id) ?>
            <?= formSelect('gas_cfdi_use_id', 'Uso CFDI Gas', $catalogs['gas_cfdi_use'], $client->gas_cfdi_use_id) ?>
            <?= formSelect('service_cfdi_use_id', 'Uso CFDI Servicio', $catalogs['service_cfdi_use'], $client->service_cfdi_use_id) ?>
        </div>
    </div>

    <?= formCheckbox('active', 'Activo', $client->active) ?>

    <div class="form-actions">
        <button type="submit">Actualizar</button>
        <a href="/views/admin/client/list-client.php" class="dynamic-link cancel-btn">Cancelar</a>
    </div>
</form>
