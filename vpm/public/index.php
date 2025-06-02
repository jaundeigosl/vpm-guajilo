<?php
require_once __DIR__ . '/../app/init.php';

use App\helpers\JwtHelper;

$currentUser = null;

if (isset($_COOKIE['jwt_token'])) {
    try {
        $decoded = JwtHelper::verifyToken($_COOKIE['jwt_token']);
        $currentUser = [
            'userId' => $decoded->data->userId,
            'roles' => $decoded->data->roles ?? []
        ];
    } catch (\Exception $e) {
        // Token inválido, eliminar cookie
        setcookie('jwt_token', '', time() - 3600, '/');
    }
}

// Si no hay usuario válido, redirigir al login
if (!$currentUser) {
    header('Location: /auth/login.php');
    exit;
}

// Continuar con la carga de la app...
header('Location: /views/admin/dashboard.php');
exit;
