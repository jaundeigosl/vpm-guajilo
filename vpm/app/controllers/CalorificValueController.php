<?php

namespace App\controllers;

use App\models\CalorificValue;

class CalorificValueController extends BaseController
{
    public function __construct()
    {
        parent::__construct('calorific_value', CalorificValue::class);
    }

    /**
     * Obtener valores paginados con región opcional.
     */
    public function getPaginated(int $page = 1, int $perPage = 10, ?int $regionId = null): array
    {
        $offset = ($page - 1) * $perPage;

        $qb = $this->queryBuilder
            ->select($this->table)
            ->limit($perPage)
            ->offset($offset);

        if ($regionId !== null) {
            $qb->where('region_id', $regionId);
        }

        $results = $qb->get();
        return array_map(fn($row) => new $this->modelClass($row), $results);
    }

    /**
     * Contar registros totales (con o sin filtro por región).
     */
    public function getTotalCount(?int $regionId = null): int
    {
        if ($regionId !== null) {
            $row = $this->queryBuilder
                ->select($this->table)
                ->where('region_id', $regionId)
                ->first();
        } else {
            $row = $this->queryBuilder
                ->raw("SELECT COUNT(*) as count FROM {$this->table}")
                ->first();
        }

        return $row['count'] ?? 0;
    }

    /**
     * Obtener todos los valores por región.
     */
    public function getByRegion(int $regionId): array
    {
        $results = $this->queryBuilder
            ->select($this->table)
            ->where('region_id', $regionId)
            ->get();

        return array_map(fn($row) => new $this->modelClass($row), $results);
    }

    /**
     * Crear múltiples valores calóricos para un mes completo.
     */
    public function createForMonth(int $regionId, int $year, int $month, array $days): array
    {
        try {
            foreach ($days as $day => $value) {
                $this->create([
                    'day' => (int)$day,
                    'month' => $month,
                    'year' => $year,
                    'region_id' => $regionId,
                    'calorific_value' => (float)$value,
                ]);
            }

            return [
                'success' => true,
                'message' => 'Valores calóricos guardados correctamente.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al guardar los valores: ' . $e->getMessage()
            ];
        }
    }

    public function getPaginatedFiltered(?int $year, ?int $month, ?int $regionId, ?int $page, ?int $perPage): array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($regionId !== null) {
            $sql .= " AND region_id = ?";
            $params[] = $regionId;
        }

        if ($month !== null) {
            $sql .= " AND month = ?";
            $params[] = $month;
        }

        if ($year !== null) {
            $sql .= " AND year = ?";
            $params[] = $year;
        }

        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        $results = $this->queryBuilder
            ->rawWithParams($sql, $params)
            ->get();

        return array_map(fn($row) => new $this->modelClass($row), $results);
    }

    public function getTotalCountFiltered(?int $year, ?int $month, ?int $regionId): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($regionId !== null) {
            $sql .= " AND region_id = ?";
            $params[] = $regionId;
        }

        if ($month !== null) {
            $sql .= " AND month = ?";
            $params[] = $month;
        }

        if ($year !== null) {
            $sql .= " AND year = ?";
            $params[] = $year;
        }

        $row = $this->queryBuilder
            ->rawWithParams($sql, $params)
            ->first();

        return $row['count'] ?? 0;
    }

    public function delete(int $id): bool
    {
        return $this->queryBuilder
                ->delete($this->table)
                ->where('id', $id)
                ->execute() !== false;
    }
}