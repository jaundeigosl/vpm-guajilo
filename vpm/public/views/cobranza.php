<?php

require_once __DIR__ . '../../../app/init.php';
use App\config\db;
use App\lib\QueryBuilder;

$db = new db('db', 'facturacion_db', 'my_user', '18082001*');
$pdo = $db->connect();

if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo conectar a la base de datos']);
    exit;
}

$qb = new QueryBuilder($pdo);

$facturasPagadas = $qb->select('facturas')->where('estatus', 'PAGADO')->get();

foreach($facturasPagadas as $key => $factura) {
    // Fetch client data for each invoice
    $cliente = $qb->select('client')->where('id', $factura['cliente_id'])->first();
    
    // Add client name to the current invoice array
    $facturasPagadas[$key]['cliente_nombre'] = $cliente['name'];
}


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tabla de Cuentas por Cobrar</title>
</head>
<body>
    <section>
        <h2>Facturas pagadas</h2>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Factura</th>
                    <th>Concepto</th>
                    <th>Fecha</th>
                    <th>Moneda</th>
                    <th>Subtotal</th>
                    <th>Iva</th>
                    <th>Total</th>
                    <th>Abono</th>
                    <th>NC</th>
                    <th>Monto NC</th>
                    <th>Saldo Factura</th>
                    <th>Proyecto</th>
                    <th>Estatus</th>
                    <th>Fecho Pago</th>
                    <th>Vencimiento</th>
                    <th>Vencidos</th>
                    <th>Comentarios</th>
                    <th>Complento</th>
                    <th>Al corriente</th>
                    <th>Rango 1 al 15</th>
                    <th>Rango 16 al 30</th>
                    <th>Rango 31 al 45</th>
                    <th>Rango 46 al 60</th>
                    <th>Rango 61 al 90</th>
                    <th>Rango mas del 90</th>
                </tr>   
            </thead>
            <tbody>
                <?php foreach($facturasPagadas as $factura): ?>
                    <tr>
                        <td><?= htmlspecialchars($factura['cliente_nombre'] ?? '') ?></td>
                        <td><?= htmlspecialchars($factura['factura'] ?? '') ?></td>
                        <td><?= htmlspecialchars($factura['concepto'] ?? '') ?></td>
                        <td><?= htmlspecialchars($factura['fecha'] ?? '') ?></td>
                        <td><?= htmlspecialchars($factura['moneda'] ?? '') ?></td>
                        <td><?= htmlspecialchars($factura['subtotal'] ?? '0') ?></td>
                        <td><?= htmlspecialchars($factura['iva'] ?? '0') ?></td>
                        <td><?= htmlspecialchars($factura['total'] ?? '0') ?></td>
                        <td><?= htmlspecialchars($factura['abono'] ?? '0') ?></td>
                        <td><?= htmlspecialchars($factura['nc'] ?? '') ?></td>
                        <td><?= htmlspecialchars($factura['monto_nc'] ?? '0') ?></td>
                        <td><?= htmlspecialchars($factura['saldo_factura'] ?? '0') ?></td>
                        <td><?= htmlspecialchars($factura['proyecto'] ?? '') ?></td>
                        <td><?= htmlspecialchars($factura['estatus'] ?? '') ?></td>
                        <td><?= htmlspecialchars($factura['fecha_pago'] ?? '') ?></td>
                        <td><?= htmlspecialchars($factura['vencimiento'] ?? '') ?></td>
                        <td><?= htmlspecialchars($factura['vencidos'] ?? '0') ?></td>
                        <td><?= htmlspecialchars($factura['comentarios'] ?? '') ?></td>
                        <td><?= htmlspecialchars($factura['complemento'] ?? '') ?></td>
                        <td><?= htmlspecialchars($factura['al_corriente'] ?? '0') ?></td>
                        <td><?= htmlspecialchars($factura['rango_1_15'] ?? '0') ?></td>
                        <td><?= htmlspecialchars($factura['rango_16_30'] ?? '0') ?></td>
                        <td><?= htmlspecialchars($factura['rango_31_45'] ?? '0') ?></td>
                        <td><?= htmlspecialchars($factura['rango_46_60'] ?? '0') ?></td>
                        <td><?= htmlspecialchars($factura['rango_61_90'] ?? '0') ?></td>
                        <td><?= htmlspecialchars($factura['rango_mas_90'] ?? '0') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</body>