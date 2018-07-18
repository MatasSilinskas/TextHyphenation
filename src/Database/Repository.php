<?php

namespace TextHyphenation\Database;

abstract class Repository
{
    protected $database;
    protected $queryBuilder;

    public function __construct(TextHyphenationDatabase $database)
    {
        $this->database = $database;
        $this->queryBuilder = new QueryBuilder();
    }

    /**
     * @return int|null
     */
    protected function checkRows(): ?int
    {
        if ($this->prepareAndExecute($affectedRows) === false) {
            return null;
        }

        return $affectedRows;
    }

    /**
     * @param int $affectedRows
     * @return array
     */
    protected function prepareAndExecute(&$affectedRows = 0): array
    {
        $result = $this->database
            ->prepare($this->queryBuilder->getQuery())
            ->execute($this->queryBuilder->getParams(), $affectedRows);

        $this->queryBuilder->reset();
        return $result;
    }
}
