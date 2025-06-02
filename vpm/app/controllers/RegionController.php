<?php

namespace App\controllers;

use App\models\Region;

class RegionController extends BaseController
{
    public function __construct()
    {
        parent::__construct('region', Region::class);
    }

    /**
     * Obtener todas las regiones activas.
     */
    public function getActive(): array
    {
        $results = $this->queryBuilder
            ->select($this->table)
            ->where('active', 1)
            ->get();

        return array_map(fn($row) => new $this->modelClass($row), $results);
    }

    /**
     * Buscar regiones por nombre.
     */
    public function findByName(string $name): array
    {
        $results = $this->queryBuilder
            ->select($this->table)
            ->whereLike('name', "%{$name}%")
            ->get();

        return array_map(fn($row) => new $this->modelClass($row), $results);
    }

    /**
     * Paginación de regiones.
     */
    public function getPaginated(int $page = 1, int $perPage = 10, bool $includeInactive = false): array
    {
        $offset = ($page - 1) * $perPage;

        $qb = $this->queryBuilder
            ->select($this->table)
            ->limit($perPage)
            ->offset($offset);

        if (!$includeInactive) {
            $qb->where('active', 1);
        }

        $results = $qb->get();

        return array_map(fn($row) => new $this->modelClass($row), $results);
    }

    /**
     * Total de regiones (útil para paginación).
     */
    public function getTotalCount(bool $includeInactive = false): int
    {
        $qb = $this->queryBuilder;

        if ($includeInactive) {
            $row = $qb->raw("SELECT COUNT(*) as count FROM {$this->table}")->first();
        } else {
            $row = $qb->select($this->table)->where('active', 1)->first();
        }

        return $row['count'] ?? 0;
    }
}