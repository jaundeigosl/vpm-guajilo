<?php
require_once __DIR__ . '/../../../app/init.php';

use App\controllers\CalorificValueController;
use App\controllers\RegionController;
use App\middleware\AuthMiddleware;

header('Content-Type: text/html; charset=utf-8');
AuthMiddleware::requireRole(['admin']);

$calorificController = new CalorificValueController();
$regionController = new RegionController();

// Regiones disponibles
$regions = $regionController->getAll();

// Parámetros de filtro
$applyFilter = isset($_GET['apply_filters']);
$selectedRegionId = $applyFilter && isset($_GET['region_id']) && $_GET['region_id'] !== '' ? (int)$_GET['region_id'] : null;
$monthParam = $applyFilter && isset($_GET['month']) && preg_match('/^\d{4}-\d{2}$/', $_GET['month']) ? $_GET['month'] : null;

[$selectedYear, $selectedMonth] = $monthParam ? explode('-', $monthParam) : [null, null];
$selectedYear = $selectedYear !== null ? (int)$selectedYear : null;
$selectedMonth = $selectedMonth !== null ? (int)$selectedMonth : null;

// Paginación
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;

// Datos
if ($applyFilter && $selectedMonth && $selectedYear) {
    $totalItems = $calorificController->getTotalCountFiltered($selectedYear, $selectedMonth, $selectedRegionId);
    $values = $calorificController->getPaginatedFiltered($selectedYear, $selectedMonth, $selectedRegionId, $currentPage, $perPage);
} else {
    $totalItems = $calorificController->getTotalCount();
    $values = $calorificController->getPaginated($currentPage, $perPage);
}

// Base URL
$baseUrl = $_SERVER['PHP_SELF'];
$queryParams = [];

if ($applyFilter) $queryParams['apply_filters'] = 1;
if ($selectedRegionId) $queryParams['region_id'] = $selectedRegionId;
if ($monthParam) $queryParams['month'] = $monthParam;
?>

<h2>Valores de Poder Calórico</h2>

<a href="/views/calorific_value/create-calorific-value.php" class="dynamic-link btn-crud">➕ Crear nuevo poder
    calórico</a>

<!--<form id="filter-form" class="filters" method="GET" action="/views/calorific_value/list-calorific-value.php">-->
<!--    <label for="region-filter">Filtrar por Región:</label>-->
<!--    <select id="region-filter" name="region_id">-->
<!--        <option value="">Todas</option>-->
<!--        --><?php //foreach ($regions as $region): ?>
<!--            <option value="--><?php //= $region->id ?><!--" --><?php //= $region->id === $selectedRegionId ? 'selected' : '' ?><!-->-->
<!--                --><?php //= htmlspecialchars($region->name) ?>
<!--            </option>-->
<!--        --><?php //endforeach; ?>
<!--    </select>-->
<!---->
<!--    <label for="month-filter">Filtrar por Mes/Año:</label>-->
<!--    <input type="month" id="month-filter" name="month" value="--><?php //= $monthParam ?? '' ?><!--">-->
<!---->
<!--    <input type="hidden" name="apply_filters" value="1">-->
<!--    <button type="submit">Aplicar Filtros</button>-->
<!--</form>-->

<table class="table">
    <thead>
    <tr>
        <th>Fecha</th>
        <th>Valor</th>
        <th>Región</th>
        <th>Acciones</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($values as $val): ?>
        <tr>
            <td><?= sprintf('%02d-%02d-%04d', $val->day, $val->month, $val->year) ?></td>
            <td><?= htmlspecialchars($val->calorific_value) ?></td>
            <td>
                <?php
                $region = array_filter($regions, fn($r) => $r->id === $val->region_id);
                echo htmlspecialchars(array_values($region)[0]->name ?? 'Desconocida');
                ?>
            </td>
            <td>
                <a href="/views/calorific_value/update-calorific-value.php?region_id=<?= $val->region_id ?>&month=<?= sprintf('%04d-%02d', $val->year, $val->month) ?>"
                   class="dynamic-link">✏️ Editar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php
include __DIR__ . '/../components/pagination.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('filter-form');

        if (form instanceof HTMLFormElement) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(form);
                const query = new URLSearchParams(formData).toString();
                const url = form.action + '?' + query;

                fetch(url)
                    .then(res => res.text())
                    .then(html => {
                        const container = document.getElementById('main-content');
                        if (container) {
                            container.innerHTML = html;
                        } else {
                            console.warn('main-content container not found');
                        }
                    })
                    .catch(err => {
                        console.error('Error al aplicar filtros:', err);
                    });
            });
        } else {
            console.error('Formulario no encontrado');
        }
    });
</script>