<?php

namespace App\controllers;

use App\models\ProductService;

class ProductServiceController extends BaseController
{
    public function __construct()
    {
        parent::__construct('products_services', ProductService::class);
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

    public function getTotalCount(): int
    {
        $row = $this->queryBuilder
            ->raw("SELECT COUNT(*) as count FROM {$this->table}")
            ->first();

        return $row['count'] ?? 0;
    }
}