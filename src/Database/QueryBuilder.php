<?php

namespace TextHyphenation\Database;

class QueryBuilder
{
    private $query = '';
    private $params = [];

    /**
     * @param array $columns
     * @return QueryBuilder
     */
    public function select(array $columns): self
    {
        $this->query .= 'SELECT ' . implode(', ', $columns) . ' ';
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function delete(): self
    {
        $this->query .= 'DELETE ';
        return $this;
    }

    /**
     * @param string $table
     * @param array $columns
     * @param array $params
     * @return QueryBuilder
     */
    public function insert(string $table, array $columns, array $params): self
    {
        $this->query .=
            "INSERT INTO $table(" . implode(', ', $columns) . ") " .
            "VALUES(" . implode(', ', array_keys($params)) . ") ";

        foreach ($params as $key => $value) {
            $this->params[$key] = $value;
        }

        return $this;
    }

    /**
     * @param array $tables
     * @return QueryBuilder
     */
    public function from(array $tables): self
    {
        $this->query .= 'FROM ' . implode(', ', $tables) . ' ';
        return $this;
    }

    /**
     * @param string $column
     * @param $params
     * @return QueryBuilder
     */
    public function where(string $column, $params): self
    {
        return $this->addWhere($column, $params);
    }

    /**
     * @param string $column
     * @param $params
     * @return QueryBuilder
     */
    public function andWhere(string $column, $params): self
    {
        return $this->addWhere($column, $params, 'AND');
    }

    /**
     * @param string $table
     * @param array $columns
     * @param $params
     * @return QueryBuilder
     */
    public function update(string $table, array $columns, $params): self
    {
        $this->query .= "UPDATE $table SET";
        $keys = array_keys($params);
        foreach ($columns as $column) {
            $key = array_shift($keys);
            $this->query .= " $column = $key," ;
            $this->params[$key] = $params[$key];
        }
        $this->query[strlen($this->query) - 1] = ' ';
        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return rtrim($this->query);
    }

    /**
     * @param string $query
     * @return QueryBuilder
     */
    public function setQuery(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function reset(): self
    {
        $this->query = '';
        $this->params = [];
        return $this;
    }

    /**
     * @param string $column
     * @param $params
     * @param string $operator
     * @return QueryBuilder
     */
    private function addWhere(string $column, $params, string $operator = 'WHERE'): self
    {
        if (is_array($params)) {
            $key = array_keys($params)[0];
            $this->params[$key] = $params[$key];
            $this->query .= "$operator $column = $key ";
            return $this;
        }
        $this->query .= "$operator $column = $params ";
        return $this;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function addParamValue(string $key, $value)
    {
        if (isset($this->params[$key])) {
            $this->params[$key] = $value;
        }
    }
}
