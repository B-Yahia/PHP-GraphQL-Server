<?php

declare(strict_types=1);

namespace Config;

use PDO;
use PDOStatement;

class Database
{
    private PDO $connection;
    private PDOStatement $stmt;

    public function __construct(string $driver, array $config, string $username, string $password)
    {
        $config = http_build_query(data: $config, arg_separator: ';');
        $dsn = $driver . ":" . $config;
        try {
            $this->connection = new PDO($dsn, $username, $password);
        } catch (\PDOException $e) {
            die("Unable to connect to DB");
        }
    }

    public function query(string $query, array $params = []): Database
    {
        $this->stmt = $this->connection->prepare($query);
        $this->stmt->execute($params);

        return $this;
    }

    public function count()
    {
        return $this->stmt->fetchColumn();
    }

    public function find()
    {
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAll()
    {
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function id()
    {
        return $this->connection->lastInsertId();
    }
}
