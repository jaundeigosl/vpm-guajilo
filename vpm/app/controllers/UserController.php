<?php

namespace App\controllers;

use App\lib\QueryBuilder;
use App\models\User;
use App\middleware\AuthMiddleware;
use Exception;

class UserController extends BaseController
{
    public function __construct()
    {
        parent::__construct('users', User::class);
    }

    /**
     * Registro de un nuevo usuario por parte del administrador.
     */
    public function adminRegister(string $name, string $lastname, string $email, string $password, string $roleName): array
    {
        try {
            AuthMiddleware::requireRole('admin');

            if (empty($name) || empty($lastname) || empty($email) || empty($password) || empty($roleName)) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            // Verificar si el email ya está en uso
            $existingEmail = $this->queryBuilder
                ->select("users")
                ->where("email", $email)
                ->first();

            if ($existingEmail) {
                throw new Exception("El email ya está en uso.");
            }

            // Buscar el rol
            $role = $this->queryBuilder
                ->select("roles")
                ->where("name", $roleName)
                ->first();

            if (!$role) {
                throw new Exception("El rol especificado no existe.");
            }

            // Crear el usuario con el role_id
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $this->create([
                "name" => $name,
                "lastname" => $lastname,
                "email" => $email,
                "password" => $passwordHash,
                "role_id" => $role['id'],
                "active" => 1
            ]);

            return [
                "success" => true,
                "message" => "Usuario registrado exitosamente con el rol '{$roleName}'."
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /**
     * Cambiar el rol de un usuario existente (solo administrador).
     */
    public function changeUserRole(int $userId, string $newRoleName): array
    {
        try {
            // Requiere que el usuario sea administrador
            AuthMiddleware::requireRole('admin');

            // Verificar si el usuario existe
            $user = $this->getById($userId);
            if (!$user) {
                throw new Exception("El usuario especificado no existe.");
            }

            // Verificar si el nuevo rol existe
            $newRole = $this->queryBuilder
                ->select("roles")
                ->where("name", $newRoleName)
                ->first();

            if (!$newRole) {
                throw new Exception("El rol especificado no existe.");
            }

            // Eliminar roles actuales del usuario
            $this->queryBuilder
                ->delete("user_roles")
                ->where("user_id", $userId)
                ->execute();

            // Asignar el nuevo rol al usuario
            $this->queryBuilder->insert("user_roles", [
                "user_id" => $userId,
                "role_id" => $newRole['id']
            ])->execute();

            return [
                "success" => true,
                "message" => "El rol del usuario ha sido actualizado a '{$newRoleName}'."
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /**
     * Editar los datos de un usuario (administrador o el propio usuario).
     */
    public function editUser(int $userId, array $data): array
    {
        try {
            global $currentUser;

            $isAdmin = in_array('admin', $currentUser['roles']);
            $isOwnProfile = $currentUser['userId'] == $userId;

            if (!$isAdmin && !$isOwnProfile) {
                throw new Exception("No tienes permiso para editar este usuario.");
            }

            $user = $this->getById($userId);
            if (!$user) {
                throw new Exception("El usuario especificado no existe.");
            }

            // Campos permitidos
            $allowedFields = ['name', 'lastname', 'email', 'password'];
            if ($isAdmin) {
                $allowedFields[] = 'role_id';
                $allowedFields[] = 'active';
            }

            $updateData = [];

            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $data)) {
                    $value = $data[$field];

                    // password vacío = no actualizar
                    if ($field === 'password') {
                        if (!empty($value)) {
                            $updateData[$field] = password_hash($value, PASSWORD_DEFAULT);
                        }
                    } elseif ($field === 'active') {
                        $updateData[$field] = (int) $value;
                    } elseif ($field === 'role_id') {
                        $updateData[$field] = (int) $value;
                    } else {
                        $updateData[$field] = trim($value);
                    }
                }
            }

            if (empty($updateData)) {
                return [
                    'success' => false,
                    'message' => "No se proporcionaron datos válidos para actualizar."
                ];
            }

            $this->queryBuilder->update($this->table, $updateData)->where('id', $userId)->execute();

            return [
                'success' => true,
                'message' => "Datos del usuario actualizados correctamente."
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Deshabilitar un usuario (solo administrador).
     */
    public function disableUser(int $userId): array
    {
        try {
            // Requiere que el usuario sea administrador
            AuthMiddleware::requireRole('admin');

            // Verificar si el usuario existe
            $user = $this->getById($userId);
            if (!$user) {
                throw new Exception("El usuario especificado no existe.");
            }

            $this->queryBuilder->update($this->table, ['active' => 0])->where('id', $userId)->execute();

            return [
                "success" => true,
                "message" => "El usuario ha sido deshabilitado correctamente."
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /**
     * Habilitar un usuario (solo administrador).
     */
    public function enableUser(int $userId): array
    {
        try {
            AuthMiddleware::requireRole('admin');

            // Verificar si el usuario existe
            $user = $this->getById($userId);
            if (!$user) {
                throw new Exception("El usuario especificado no existe.");
            }

            // Habilitar el usuario
            $this->queryBuilder->update($this->table, ['active' => 1])->where('id', $userId)->execute();

            return [
                "success" => true,
                "message" => "El usuario ha sido habilitado correctamente."
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    public function getUsersWithRoleNames(int $page = 1, int $perPage = 10, bool $includeInactive = false): array
    {
        $offset = ($page - 1) * $perPage;

        $query = "SELECT users.*, roles.name AS role_name
              FROM users
              LEFT JOIN roles ON users.role_id = roles.id";

        if (!$includeInactive) {
            $query .= " WHERE users.active = 1";
        }

        $query .= " LIMIT ? OFFSET ?";

        $results = $this->queryBuilder
            ->rawWithParams($query, [$perPage, $offset])
            ->get();

        return array_map(fn($row) => new $this->modelClass($row), $results);
    }
}