<?php
require __DIR__ . '/../../app/init.php';

use App\controllers\AuthController;

global $database;
$authController = new AuthController($database);

$authController->logout();
//require __DIR__ . '/../views/auth/login.php';
header('Location: /../views/auth/login.php');
exit;
