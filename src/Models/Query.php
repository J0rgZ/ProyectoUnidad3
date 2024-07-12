<?php

namespace App\Models;

use PDO;
use PDOException;

class Query
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = $this->getConnection();
    }

    protected function getConnection(): PDO
    {
        $host = getenv('DB_HOST') ?: 'localhost';
        $dbname = getenv('DB_NAME') ?: 'alquiler';
        $username = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            // It's a good practice to handle exceptions and not expose sensitive error details
            throw new Exception("Database connection error: " . $e->getMessage());
        }
    }

    public function selectAll(string $sql): array
    {
        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("Error en consulta SQL: " . $e->getMessage());
        }
    }

    public function select(string $sql): ?array
    {
        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            throw new PDOException("Error en consulta SQL: " . $e->getMessage());
        }
    }

    public function insertar(string $sql, array $datos): int
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($datos);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new PDOException("Error al insertar en la base de datos: " . $e->getMessage());
        }
    }

    public function save(string $sql, array $datos): int
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($datos);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new PDOException("Error al actualizar la base de datos: " . $e->getMessage());
        }
    }
}
