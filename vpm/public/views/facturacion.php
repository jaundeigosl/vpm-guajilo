<?php
require_once __DIR__ . '../../../app/init.php';

use App\config\db;

$db = new db('db', 'facturacion_db', 'my_user', '18082001*');
$pdo = $db->connect();

$estado_cliente = $_GET['estado_cliente'] ?? 'todos';
$mes = $_GET['mes'] ?? date('m');
$anio = $_GET['anio'] ?? date('Y');
$mas_de_90_dias = $_GET['mas_de_90_dias'] ?? 'no';

$sql = "
    SELECT c.name AS cliente, SUM(cxc.saldo_total) AS saldo_total, cxc.moneda
    FROM cuentas_por_cobrar cxc
    JOIN client c ON c.id = cxc.cliente_id
    WHERE MONTH(cxc.created_at) = :mes AND YEAR(cxc.created_at) = :anio
";

$params = [
    ':mes' => $mes,
    ':anio' => $anio
];

if ($estado_cliente === 'activo') {
    $sql .= " AND c.estado = 'activo'";
} elseif ($estado_cliente === 'no_activo') {
    $sql .= " AND c.estado = 'no_activo'";
}

if ($mas_de_90_dias === 'si') {
    $sql .= " AND DATEDIFF(NOW(), cxc.fecha_vencimiento) > 90";
}

$sql .= " GROUP BY c.name, cxc.moneda";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Separar por moneda
$datos_usd = [];
$datos_mxn = [];

foreach ($datos as $fila) {
    if ($fila['moneda'] === 'USD') {
        $datos_usd[] = $fila;
    } elseif ($fila['moneda'] === 'MXN') {
        $datos_mxn[] = $fila;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tabla de Cuentas por Cobrar</title>
</head>
<body>
    <h1>Resumen de Cuentas por Cobrar</h1>

    <form method="get" action="">
        <label for="estado_cliente">Estado del Cliente:</label>
        <select id="estado_cliente" name="estado_cliente">
            <option value="todos" <?= $estado_cliente == 'todos' ? 'selected' : '' ?>>Todos</option>
            <option value="activo" <?= $estado_cliente == 'activo' ? 'selected' : '' ?>>Activos</option>
            <option value="no_activo" <?= $estado_cliente == 'no_activo' ? 'selected' : '' ?>>No Activos</option>
        </select>

        <label for="mes">Mes:</label>
        <select id="mes" name="mes">
            <?php
            $meses = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
            foreach ($meses as $num => $nombre) {
                echo "<option value=\"$num\" " . ($mes == $num ? 'selected' : '') . ">$nombre</option>";
            }
            ?>
        </select>

        <label for="anio">Año:</label>
        <input type="number" id="anio" name="anio" value="<?= htmlspecialchars($anio) ?>" min="2000" max="<?= date('Y') ?>">

        <label>
            <input type="checkbox" name="mas_de_90_dias" value="si" <?= $mas_de_90_dias === 'si' ? 'checked' : '' ?>>
            Más de 90 días de vencimiento
        </label>

        <button type="submit">Filtrar</button>
    </form>

    <!-- Tabla USD -->
    <h2>Clientes con saldos en USD</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Saldo Total (USD)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($datos_usd) === 0): ?>
                <tr><td colspan="2">No hay registros en USD</td></tr>
            <?php else: ?>
                <?php foreach ($datos_usd as $fila): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['cliente']) ?></td>
                        <td><?= number_format($fila['saldo_total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tabla MXN -->
    <h2>Clientes con saldos en MXN</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Saldo Total (MXN)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($datos_mxn) === 0): ?>
                <tr><td colspan="2">No hay registros en MXN</td></tr>
            <?php else: ?>
                <?php foreach ($datos_mxn as $fila): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['cliente']) ?></td>
                        <td><?= number_format($fila['saldo_total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
