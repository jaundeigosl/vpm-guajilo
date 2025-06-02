<?php

namespace App\controllers;

use App\config\db;
use App\helpers\JwtHelper;
use Exception;
use App\lib\QueryBuilder;

class AuthController
{
    private QueryBuilder $queryBuilder;

    public function __construct(db $database)
    {
        $this->queryBuilder = new QueryBuilder($database->connect());
    }

    /**
     * Registro de usuario
     */
    public function register(string $name, string $lastname, string $email, string $password): array
    {
        try {
            if (empty($email) || empty($password)) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            // Verificar si el nombre de usuario ya está en uso
            $existingUser = $this->queryBuilder
                ->select("users")
                ->where("name", $name)
                ->first();

            if ($existingUser) {
                throw new Exception("El nombre de usuario ya está en uso.");
            }

            // Verificar si el email ya está en uso
            $existingEmail = $this->queryBuilder
                ->select("users")
                ->where("email", $email)
                ->first();

            if ($existingEmail) {
                throw new Exception("El email ya está en uso.");
            }

            // Asignar el rol predeterminado (cliente)
            $defaultRoleName = "admin";

            $role = $this->queryBuilder
                ->select("roles")
                ->where("name", $defaultRoleName)
                ->first();

            if (!$role) {
                throw new Exception("El rol por defecto no existe en la base de datos.");
            }

            // Crear el usuario
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $this->queryBuilder->insert("users", [
                "name" => $name,
                "lastname" => $lastname,
                "email" => $email,
                "password" => $passwordHash,
                "role_id" => $role['id'],
                "active" => 1
            ])->execute();

            return [
                "success" => true,
                "message" => "Usuario registrado exitosamente."
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /**
     * Login de usuario
     */
    public function login(string $email, string $password): array
    {
        try {
            // Buscar el usuario por email
            $user = $this->queryBuilder
                ->select('users')
                ->where('email', $email)
                ->where('active', 1) // <- 👈 Solo usuarios activos
                ->first();

            if (!$user || !password_verify($password, $user['password'])) {
                return [
                    'success' => false,
                    'message' => 'Correo o contraseña inválidos, o el usuario está inactivo.'
                ];
            }

            // Obtener el rol del usuario
            $role = $this->queryBuilder
                ->select("roles")
                ->where("id", $user['role_id'])
                ->first();

            $rolesNames = $role ? [$role['name']] : [];

            // Generar el token JWT
            $payload = [
                "userId" => $user['id'],
                "name" => $user['name'],
                "lastname" => $user['lastname'],
                "roles" => $rolesNames
            ];

            $jwt = JwtHelper::generateToken($payload);

            // Guardar el token en una cookie
            setcookie("jwt_token", $jwt, time() + (86400 * 30), "/");

            return [
                "success" => true,
                "message" => "Login exitoso.",
                "token" => $jwt
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /**
     * Logout de usuario
     */
    public function logout(): array
    {
        try {
            // Eliminar la cookie jwt_token
            if (isset($_COOKIE['jwt_token'])) {
                setcookie("jwt_token", "", time() - 3600, "/");
            }

            return [
                "success" => true,
                "message" => "Sesión cerrada exitosamente."
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => "Error al cerrar sesión: " . $e->getMessage()
            ];
        }
    }
}