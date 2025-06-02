<?php
require __DIR__ . '/../../app/init.php';

use App\controllers\AuthController;

global $database;
$authController = new AuthController($database);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $response = $authController->register(
        $_POST['name'] ?? '',
        $_POST['lastname'] ?? '',
        $_POST['email'] ?? '',
        $_POST['password'] ?? ''
    );

    if ($response['success']) {
        header("Location: /auth/login.php");
        exit;
    }

    $error = $response['message'];
}

require __DIR__ . '/../views/auth/register.php';
