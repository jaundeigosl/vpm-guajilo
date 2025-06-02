<?php
require_once __DIR__ . '/../../../app/init.php';

use App\controllers\ClientController;

header('Content-Type: application/json');
$controller = new ClientController();
$regionId = $_GET['region_id'] ?? null;

if ($regionId) {
    $clients = $controller->getClientsByRegion((int)$regionId);
    echo json_encode(array_map(fn($c) => [
        'id' => $c->id,
        'name' => $c->name,
        'email' => $c->email,
        'alias' => $c->alias
    ], $clients));
} else {
    echo json_encode([]);
}