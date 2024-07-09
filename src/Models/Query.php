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
        $host = 'localhost';
        $dbname = 'alquiler';
        $username = 'root';
        $password = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $pdo;
        } catch (PDOException $e) {
            exit("Error de conexión: " . $e->getMessage());
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
