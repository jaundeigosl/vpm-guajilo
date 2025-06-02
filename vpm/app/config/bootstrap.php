<?php
require dirname(__DIR__, 2) . '/vendor/autoload.php';
require_once __DIR__ . '/../lib/ErrorHandler.php';
require_once __DIR__ . '/db.php';
use App\config\db;

set_error_handler('ErrorHandler::handleError');
set_exception_handler('ErrorHandler::handleException');

$dotenv = Dotenv\Dotenv::createImmutable(dirname(dirname(__DIR__)));
$dotenv->load();

$database = new db(
    $_ENV['DB_HOST'],
    $_ENV['DB_DATABASE'],
    $_ENV['DB_USERNAME'],
    $_ENV['DB_PASSWORD']
);
