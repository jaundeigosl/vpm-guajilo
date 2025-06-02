<?php
namespace App\middleware;

use App\helpers\JwtHelper;

class AuthMiddleware
{
    /**
     * Verifica existencia y validez del JWT, y asigna \$currentUser global.
     */
    public static function requireAuth(): void
    {
        if (!isset($_COOKIE['jwt_token'])) {
            header('Location: /public/auth/login.php');
            exit;
        }

        try {
            $decoded = JwtHelper::verifyToken($_COOKIE['jwt_token']);
        } catch (\Exception $e) {
            setcookie('jwt_token', '', time() - 3600, '/');
            header('Location: /public/auth/login.php');
            exit;
        }

        // Exponer datos de usuario globalmente
        global $currentUser;
        $currentUser = [
            'userId' => $decoded->data->userId,
            'name' => $decoded->data->name ?? '',
            'lastname' => $decoded->data->lastname ?? '',
            'roles' => $decoded->data->roles ?? []
        ];
    }

    /**
     * Requiere que el usuario tenga al menos uno de los roles indicados.
     * @param string|array $requiredRoles
     */
    public static function requireRole(string|array $requiredRoles): void
    {
        self::requireAuth();
        global $currentUser;

        $userRoles = $currentUser['roles'] ?? [];
        $required = (array)$requiredRoles;

        // Comprueba intersección de roles
        if (empty(array_intersect($userRoles, $required))) {
            http_response_code(403);
            die('No tienes permiso para acceder a esta sección.');
        }
    }
}
