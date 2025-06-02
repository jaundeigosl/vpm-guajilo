<?php

namespace App\controllers;

use App\lib\QueryBuilder;
use App\models\BaseModel;

abstract class BaseController
{
    protected QueryBuilder $queryBuilder;
    protected string $table;
    protected string $modelClass;

    public function __construct(string $table, string $modelClass)
    {
        global $database;
        $this->queryBuilder = new QueryBuilder($database->connect());
        $this->table = $table;
        $this->modelClass = $modelClass;
    }

    /**
     * Crear un nuevo registro.
     */
    public function create(array $data): bool
    {
        return $this->queryBuilder->insert($this->table, $data)->execute() !== false;
    }

    /**
     * Obtener todos los registros como objetos del modelo.
     */
    public function getAll(): array
    {
        $results = $this->queryBuilder->select($this->table)->get();
        return array_map(fn($row) => new $this->modelClass($row), $results);
    }

    /**
     * Obtener un registro por ID como objeto del modelo.
     */
    public function getById(int $id): ? BaseModel
    {
        $row = $this->queryBuilder->select($this->table)->where('id', $id)->first();
        return $row ? new $this->modelClass($row) : null;
    }

    /**
     * Actualizar un registro existente.
     */
    public function update(int $id, array $data): bool
    {
        return $this->queryBuilder->update($this->table, $data)->where('id', $id)->execute() !== false;
    }

    /**
     * Eliminar un registro (lógicamente).
     */
    public function delete(int $id): bool
    {
        return $this->queryBuilder->update($this->table, ['active' => 0])->where('id', $id)->execute() !== false;
    }

    /**
     * Habilitar un registro.
     */
    public function enable(int $id): bool
    {
        return $this->queryBuilder->update($this->table, ['active' => 1])->where('id', $id)->execute() !== false;
    }

    /**
     * Deshabilitar un registro.
     */
    public function disable(int $id): bool
    {
        return $this->queryBuilder->update($this->table, ['active' => 0])->where('id', $id)->execute() !== false;
    }

    /**
     * Obtener roles paginados.
     * Ideal para cargar tablas por secciones SPA.
     */
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

    /**
     * Obtener cantidad total de registros.
     * Útil para saber cuántas páginas mostrar.
     */
    public function getTotalCount(): int
    {
        $row = $this->queryBuilder
            ->raw("SELECT COUNT(*) as count FROM {$this->table}")
            ->first();

        return $row['count'] ?? 0;
    }
}