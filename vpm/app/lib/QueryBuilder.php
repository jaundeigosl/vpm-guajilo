<?php
namespace App\lib;

use PDO;
use PDOException;

class QueryBuilder
{
    private PDO $pdo;
    private string $query = '';
    private array $params = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Limpia la consulta y parámetros antes de construir una nueva operación
     */
    private function reset(): void
    {
        $this->query = '';
        $this->params = [];
    }

    /**
     * SELECT
     * Uso:
     *    $qb->select('users')->where('id', 10)->get();
     */
    public function select(string $table, $columns = '*'): self
    {
        $this->reset();
        $cols = is_array($columns) ? implode(',', $columns) : $columns;
        $this->query = "SELECT $cols FROM $table";
        return $this;
    }

    /**
     * WHERE
     *   - Encadenable con select, update, delete.
     *   - No usar con insert() (INSERT + WHERE no es SQL válido).
     */
    public function where(string $column, $value, string $operator = '='): self
    {
        if (stripos($this->query, 'WHERE') === false) {
            $this->query .= " WHERE $column $operator ?";
        } else {
            $this->query .= " AND $column $operator ?";
        }
        $this->params[] = $value;
        return $this;
    }

    /**
     * INSERT
     * Uso:
     *    $qb->insert('users', ['username' => 'ana', 'email' => 'ana@example.com'])->execute();
     */
    public function insert(string $table, array $data): self
    {
        $this->reset();
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $this->query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        // Pasamos los valores sin anidar arrays
        $this->params = array_values($data);
        return $this;
    }

    /**
     * UPDATE
     * Uso:
     *    $qb->update('users', ['username' => 'nuevoNombre'])->where('id', 10)->execute();
     */
    public function update(string $table, array $data): self
    {
        $this->reset();
        $setClause = implode(', ', array_map(fn($key) => "$key = ?", array_keys($data)));
        $this->query = "UPDATE $table SET $setClause";
        $this->params = array_values($data);
        return $this;
    }

    /**
     * DELETE
     * Uso:
     *    $qb->delete('users')->where('id', 10)->execute();
     */
    public function delete(string $table): self
    {
        $this->reset();
        $this->query = "DELETE FROM $table";
        return $this;
    }

    /**
     * Ejecuta la consulta preparada y devuelve el PDOStatement
     */
    public function execute()
    {
        try {
            $stmt = $this->pdo->prepare($this->query);
            $stmt->execute($this->params);
            return $stmt;
        } catch (PDOException $e) {
            die("Query Failed: " . $e->getMessage());
        }
    }

    /**
     * Devuelve todos los registros como un array asociativo
     */
    public function get(): array
    {
        return $this->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Devuelve la primera fila como array asociativo o null si no hay resultados
     */
    public function first(): ?array
    {
        $result = $this->execute()->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function whereLike(string $column, string $value): self
    {
        return $this->where($column, $value, 'LIKE');
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->query .= " ORDER BY $column $direction";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->query .= " LIMIT ?";
        $this->params[] = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->query .= " OFFSET ?";
        $this->params[] = $offset;
        return $this;
    }

    public function raw(string $sql): self
    {
        $this->reset();
        $this->query = $sql;
        return $this;
    }

    public function rawWithParams(string $sql, array $params = []): self
    {
        $this->reset();
        $this->query = $sql;
        $this->params = $params;
        return $this;
    }

}
