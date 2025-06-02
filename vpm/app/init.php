<?php
declare(strict_types = 1);
global $database;
use Dotenv\Dotenv;

global $database;
require __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


if (!isset($database)) {
    die("Error: No se pudo conectar a la base de datos.");
}

$database->connect();
