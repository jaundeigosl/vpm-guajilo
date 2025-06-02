<?php

namespace App\controllers;

use App\config\db;
use App\models\Role;

class RoleController extends BaseController
{
    public function __construct()
    {
        parent::__construct('roles', Role::class);
    }

    /**
     * Obtener todos los roles activos.
     */
    public function getActive(): array
    {
        $results = $this->queryBuilder
            ->select($this->table)
            ->get();

        return array_map(fn($row) => new $this->modelClass($row), $results);
    }

    /**
     * Buscar roles por nombre.
     */
    public function findByName(string $name): array
    {
        $results = $this->queryBuilder
            ->select($this->table)
            ->whereLike('name', "%{$name}%")
            ->get();

        return array_map(fn($row) => new $this->modelClass($row), $results);
    }
}
