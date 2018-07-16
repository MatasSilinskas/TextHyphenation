<?php

namespace TextHyphenation\Database;

use PDO;

abstract class Database
{
    private $database;
    private $transactionFailed = false;
    /**
     * @var \PDOStatement $statement
     */
    private $statement;

    /**
     * Database constructor.
     * @param string $dsn
     * @param string $username
     * @param string $password
     */
    public function __construct(string $dsn, string $username, string $password)
    {
        $this->database = new PDO($dsn, $username, $password);
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
    public function createDatabase(string $database): void
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
     * @param int $affectedRows
     * @return array|bool
     */
    public function execute(array $params, &$affectedRows = 0)
    {
        foreach ($params as $key => $value) {
            $this->statement->bindValue($key, $value);
        }
        $this->statement->execute();
        if (($result = $this->statement->fetchAll(PDO::FETCH_ASSOC)) === false) {
            if ($this->database->inTransaction()) {
                $this->transactionFailed = true;
                return false;
            }
        }

        $affectedRows = $this->statement->rowCount();
        return $result;
    }

    public function executeAndGet(array $params)
    {
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
        if ($this->transactionFailed) {
            $this->database->rollBack();
            $this->transactionFailed = false;
            return false;
        }
        return $this->database->commit();
    }
}
