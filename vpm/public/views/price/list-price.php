<?php
require_once __DIR__ . '/../../../app/init.php';

use App\controllers\PriceController;
use App\middleware\AuthMiddleware;

header('Content-Type: text/html; charset=utf-8');
AuthMiddleware::requireRole(['admin']);

$priceController = new PriceController();

// Filtros
$applyFilter = isset($_GET['apply_filters']);
$monthParam = $applyFilter && isset($_GET['month']) && preg_match('/^\d{4}-\d{2}$/', $_GET['month']) ? $_GET['month'] : null;
[$selectedYear, $selectedMonth] = $monthParam ? explode('-', $monthParam) : [null, null];

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;

// Datos
if ($applyFilter && $selectedMonth && $selectedYear) {
    $totalItems = $priceController->getTotalCountFiltered((int)$selectedYear, (int)$selectedMonth);
    $prices = $priceController->getPaginatedFiltered((int)$selectedYear, (int)$selectedMonth, $currentPage, $perPage);
} else {
    $totalItems = $priceController->getTotalCount();
    $prices = $priceController->getPaginated($currentPage, $perPage);
}

$baseUrl = $_SERVER['PHP_SELF'];
$queryParams = [];
if ($applyFilter) {
    $queryParams['apply_filters'] = 1;
    if ($monthParam) $queryParams['month'] = $monthParam;
}
?>

<h2>Precios Diarios</h2>

<a href="/views/price/create-price.php" class="dynamic-link btn-crud">➕ Crear precios del mes</a>

<form id="filter-form" class="filters" method="GET" action="/views/price/list-price.php">
    <label for="month-filter">Filtrar por Mes/Año:</label>
    <input type="month" id="month-filter" name="month" value="<?= $monthParam ?? '' ?>">
    <input type="hidden" name="apply_filters" value="1">
    <button type="submit">Aplicar Filtros</button>
</form>

<table class="table">
    <thead>
    <tr>
        <th>Día</th>
        <th>HSC Diario (USD/MMBtu)</th>
        <th>TC Diario Reuters (Pesos/USD)</th>
        <th>Acciones</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($prices as $p): ?>
        <tr>
            <td><?= $p->day ?></td>
            <td><?= htmlspecialchars($p->daily_hsc_price) ?></td>
            <td><?= htmlspecialchars($p->daily_exchange_rate) ?></td>
            <td>
                <a href="/views/price/update-price.php?month=<?= sprintf('%04d-%02d', $p->year, $p->month) ?>"
                   class="dynamic-link btn-crud">✏️ Editar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../components/pagination.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('filter-form');

        if (form instanceof HTMLFormElement) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(form);
                const query = new URLSearchParams(formData).toString();
                const url = `list-price.php?${query}`;

                fetch(url)
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('main-content').innerHTML = html;
                    })
                    .catch(err => {
                        console.error('Error al aplicar filtros:', err);
                        document.getElementById('main-content').innerHTML = '<p>Error al aplicar filtros.</p>';
                    });
            });
        }
    });
</script>