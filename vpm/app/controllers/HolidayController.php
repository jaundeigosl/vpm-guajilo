<?php

namespace App\controllers;

use App\models\Holiday;

class HolidayController extends BaseController
{
    public function __construct()
    {
        parent::__construct('holidays', Holiday::class);
    }

    /**
     * Obtener registros paginados.
     */
    public function getPaginated(int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;

        $results = $this->queryBuilder
            ->rawWithParams("SELECT * FROM holidays ORDER BY date DESC LIMIT ? OFFSET ?", [$perPage, $offset])
            ->get();

        return array_map(fn($row) => new $this->modelClass($row), $results);
    }

    /**
     * Obtener total de registros.
     */
    public function getTotalCount(): int
    {
        return $this->queryBuilder
            ->raw("SELECT COUNT(*) as count FROM {$this->table}")
            ->first()['count'] ?? 0;
    }

    /**
     * Obtener todos los días festivos de un año.
     */
    public function getByYear(int $year): array
    {
        $results = $this->queryBuilder
            ->select($this->table)
            ->where('year', $year)
            ->get();

        return array_map(fn($row) => new $this->modelClass($row), $results);
    }
}