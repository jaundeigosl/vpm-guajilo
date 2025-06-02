<?php

namespace App\controllers;

use App\models\BillingPeriod;

class BillingPeriodController extends BaseController
{
    public function __construct()
    {
        parent::__construct('billing_periods', BillingPeriod::class);
    }

    /**
     * Obtener lista paginada con opción de incluir registros inactivos.
     */
    public function getPaginated(int $page = 1, int $perPage = 10, bool $includeInactive = false): array
    {
        $offset = ($page - 1) * $perPage;

        $query = "SELECT * FROM {$this->table}";
        $params = [];

        if (!$includeInactive) {
            $query .= " WHERE active = 1";
        }

        $query .= " LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        $results = $this->queryBuilder
            ->rawWithParams($query, $params)
            ->get();

        return array_map(fn($row) => new $this->modelClass($row), $results);
    }

    /**
     * Contar registros totales con opción de incluir inactivos.
     */
    public function getTotalCount(bool $includeInactive = false): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];

        if (!$includeInactive) {
            $sql .= " WHERE active = ?";
            $params[] = 1;
        }

        $row = $this->queryBuilder
            ->rawWithParams($sql, $params)
            ->first();

        return $row['count'] ?? 0;
    }
}