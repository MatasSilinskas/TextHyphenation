<?php

namespace TextHyphenation\Database;


use PDO;

abstract class Database
{
    private $database;
    /**
     * @var \PDOStatement $statement
     */
    private $statement;

    /**
     * Database constructor.
     */
    public function __construct()
    {
        $this->database = new PDO('mysql:host=localhost', 'root', 'password');
    }

    /**
     * @param string $table
     * @param array $columns
     */
    public function createTable(string $table, array $columns)
    {
        $this->database->exec("CREATE TABLE IF NOT EXISTS $table(" . implode(', ', $columns) . ')');
    }

    /**
     * @param string $database
     */
    public function createDatabase(string $database) : void
    {
        $this->database->exec("CREATE DATABASE IF NOT EXISTS $database");
        $this->database->exec("USE $database");
    }

    /**
     * @param string $table
     */
    public function dropTable(string $table): void
    {
        $this->database->exec("DROP TABLE $table");
    }

    /**
     * @param string $query
     * @return $this
     */
    public function prepare(string $query): self
    {
        $this->statement = $this->database->prepare($query);
        return $this;
    }

    /**
     * @param array $params
     * @return array
     */
    public function execute(array $params): array
    {
        foreach ($params as $key => $value) {
            $this->statement->bindValue($key, $value);
        }
        $this->statement->execute();
//        var_dump($this->statement->errorInfo());
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return Database
     */
    public function beginTransaction(): self
    {
        $this->database->beginTransaction();
        return $this;
    }

    /**
     * @return bool
     */
    public function commit(): bool
    {
        return $this->database->commit();
    }
}