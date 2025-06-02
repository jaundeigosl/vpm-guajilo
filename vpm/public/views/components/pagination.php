<?php
/**
 * Componente de paginación reutilizable para SPA
 *
 * Espera:
 * - $totalItems
 * - $currentPage
 * - $perPage
 * - $baseUrl → ruta sin query (ej: 'list-user.php')
 * - $queryParams → array opcional con parámetros extra como filtros (ej: ['region_id' => 1])
 */

$totalPages = ceil($totalItems / $perPage);
$queryParams = $queryParams ?? []; // por si no se define
?>

<?php if ($totalPages > 1): ?>
    <nav class="pagination">
        <!-- Botón anterior -->
        <?php if ($currentPage > 1): ?>
            <?php
            $prevParams = array_merge($queryParams, ['page' => $currentPage - 1]);
            $prevUrl = $baseUrl . '?' . http_build_query($prevParams);
            ?>
            <button class="pagination-btn dynamic-link" data-url="<?= $prevUrl ?>">
                &laquo; Anterior
            </button>
        <?php endif; ?>

        <!-- Números de página -->
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php
            $pageParams = array_merge($queryParams, ['page' => $i]);
            $pageUrl = $baseUrl . '?' . http_build_query($pageParams);
            ?>
            <button class="pagination-btn dynamic-link <?= $i === $currentPage ? 'active' : '' ?>"
                    data-url="<?= $pageUrl ?>">
                <?= $i ?>
            </button>
        <?php endfor; ?>

        <!-- Botón siguiente -->
        <?php if ($currentPage < $totalPages): ?>
            <?php
            $nextParams = array_merge($queryParams, ['page' => $currentPage + 1]);
            $nextUrl = $baseUrl . '?' . http_build_query($nextParams);
            ?>
            <button class="pagination-btn dynamic-link" data-url="<?= $nextUrl ?>">
                Siguiente &raquo;
            </button>
        <?php endif; ?>
    </nav>
<?php endif; ?>