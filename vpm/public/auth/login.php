<?php
require __DIR__ . '/../../app/init.php';

use App\controllers\AuthController;
use App\helpers\JwtHelper;

global $database;
$authController = new AuthController($database);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $response = $authController->login(
        $_POST['email'] ?? '',
        $_POST['password'] ?? ''
    );

    if ($response['success']) {
        session_start();
        $_SESSION['token'] = $response['token'];

        $decoded = JwtHelper::verifyToken($response['token']);
        $roles = isset($decoded->data->roles) ? (array)$decoded->data->roles : [];


        if (in_array('admin', $roles)) {
            header("Location: ../views/admin/dashboard.php");
        } elseif (in_array('editor', $roles)) {
            header("Location: ../views/editor/dashboard.php");
        } else {
            header("Location: ../views/client/dashboard.php");
        }
        exit;
    }

    $error = $response['message'];
}

require __DIR__ . '/../views/auth/login.php';
