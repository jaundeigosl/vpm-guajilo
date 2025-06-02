<?php
namespace App\config;

use PDO;
use PDOException;

class db
{
    private ?PDO $connection = null;
    private string $host;
    private string $name;
    private string $user;
    private string $password;

    public function __construct(
        string $host,
        string $email,
        string $user,
        string $password
    )
    {
        $this->password = $password;
        $this->user = $user;
        $this->name = $email;
        $this->host = $host;
    }

    public function connect(): ?PDO
    {
        try {
            if ($this->connection === null) {
                $this->connection = new PDO("mysql:host=$this->host;dbname=$this->name", $this->user, $this->password);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $this->connection->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
//                echo "Connected successfully";
            }
            return $this->connection;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
}