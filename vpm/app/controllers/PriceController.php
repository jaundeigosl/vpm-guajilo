<?php

namespace App\controllers;

use App\models\Price;

class PriceController extends BaseController
{
    public function __construct()
    {
        parent::__construct('prices', Price::class);
    }

    public function getByMonthYear(int $year, int $month): array
    {
        $results = $this->queryBuilder
            ->select($this->table)
            ->where('year', $year)
            ->where('month', $month)
            ->get();

        return array_map(fn($row) => new $this->modelClass($row), $results);
    }

    public function delete(int $id): bool
    {
        return $this->queryBuilder
                ->delete($this->table)
                ->where('id', $id)
                ->execute() !== false;
    }

    public function getTotalCount(): int
    {
        $row = $this->queryBuilder
            ->raw("SELECT COUNT(*) as count FROM {$this->table}")
            ->first();

        return $row['count'] ?? 0;
    }

    public function getPaginated(int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;

        $results = $this->queryBuilder
            ->select($this->table)
            ->limit($perPage)
            ->offset($offset)
            ->get();

        return array_map(fn($row) => new $this->modelClass($row), $results);
    }

    public function getTotalCountFiltered(?int $year, ?int $month): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE 1=1";
        $params = [];

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

    public function getPaginatedFiltered(?int $year, ?int $month, ?int $page, ?int $perPage): array
    {
        $offset = ($page - 1) * $perPage;

        $qb = $this->queryBuilder
            ->select($this->table)
            ->limit($perPage)
            ->offset($offset);

        if ($month !== null) {
            $qb->where('month', $month);
        }

        if ($year !== null) {
            $qb->where('year', $year);
        }

        $results = $qb->get();
        return array_map(fn($row) => new $this->modelClass($row), $results);
    }
}