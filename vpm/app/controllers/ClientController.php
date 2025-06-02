<?php

namespace App\controllers;

use App\models\Client;

class ClientController extends BaseController
{
    public function __construct()
    {
        parent::__construct('client', Client::class);
    }

    public function getPaginated(int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;

        $rows = $this->queryBuilder
            ->select($this->table)
            ->limit($perPage)
            ->offset($offset)
            ->get();

        return array_map(fn($row) => new $this->modelClass($row), $rows);
    }

    public function getTotalCount(bool $includeInactive = false): int
    {
        if ($includeInactive) {
            $row = $this->queryBuilder
                ->raw("SELECT COUNT(*) as count FROM client")
                ->first();
        } else {
            $row = $this->queryBuilder
                ->rawWithParams("SELECT COUNT(*) as count FROM client WHERE active = ?", [1])
                ->first();
        }

        return $row['count'] ?? 0;
    }

    public function getClientsWithRelations(int $page = 1, int $perPage = 10, bool $includeInactive = false): array
    {
        $offset = ($page - 1) * $perPage;

        $query = "
        SELECT client.*, 
               region.name AS region_name,
               productive_sector.name AS sector_name,
               currency_molecule.name AS currency_molecule_name,
               currency_service.name AS currency_service_name,
               molecule_unit.name AS molecule_unit_name,
               service_unit.name AS service_unit_name,
               billing_unit.name AS billing_unit_name,
               billing_periods.name AS billing_period_name,
               gas_cfdi_use.name AS gas_cfdi_name,
               service_cfdi_use.name AS service_cfdi_name
        FROM client
        LEFT JOIN region ON client.region_id = region.id
        LEFT JOIN productive_sector ON client.sector_id = productive_sector.id
        LEFT JOIN currency_molecule ON client.currency_molecule_id = currency_molecule.id
        LEFT JOIN currency_service ON client.currency_service_id = currency_service.id
        LEFT JOIN molecule_unit ON client.molecule_unit_id = molecule_unit.id
        LEFT JOIN service_unit ON client.service_unit_id = service_unit.id
        LEFT JOIN billing_unit ON client.billing_unit_id = billing_unit.id
        LEFT JOIN billing_periods ON client.billing_period_id = billing_periods.id
        LEFT JOIN gas_cfdi_use ON client.gas_cfdi_use_id = gas_cfdi_use.id
        LEFT JOIN service_cfdi_use ON client.service_cfdi_use_id = service_cfdi_use.id
    ";

        if (!$includeInactive) {
            $query .= " WHERE client.active = 1";
        }

        $query .= " LIMIT ? OFFSET ?";

        $results = $this->queryBuilder->rawWithParams($query, [$perPage, $offset])->get();

        return array_map(fn($row) => new $this->modelClass($row), $results);
    }
    public function getCatalogItems(string $table): array
    {
        return $this->queryBuilder
            ->select($table)
            ->where('active', 1)
            ->get();
    }

    public function getAllCatalogs(): array
    {
        return [
            'regions' => $this->getCatalogItems('region'),
            'sectors' => $this->getCatalogItems('productive_sector'),
            'currency_molecule' => $this->getCatalogItems('currency_molecule'),
            'currency_service' => $this->getCatalogItems('currency_service'),
            'molecule_unit' => $this->getCatalogItems('molecule_unit'),
            'service_unit' => $this->getCatalogItems('service_unit'),
            'billing_unit' => $this->getCatalogItems('billing_unit'),
            'billing_period' => $this->getCatalogItems('billing_periods'),
            'gas_cfdi_use' => $this->getCatalogItems('gas_cfdi_use'),
            'service_cfdi_use' => $this->getCatalogItems('service_cfdi_use'),
        ];
    }

    public function getClientsByRegion(int $regionId): array
    {
        $results = $this->queryBuilder
            ->select('client')
            ->where('region_id', $regionId)
            ->where('active', 1)
            ->get();

        return array_map(fn($row) => new $this->modelClass($row), $results);
    }

    public function getRegions(): array
    {
        $results = $this->queryBuilder
            ->select('region')
            ->where('active', 1)
            ->get();

        return $results;
    }
}