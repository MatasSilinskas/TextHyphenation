<?php

namespace TextHyphenation\Tests;

use PHPUnit\Framework\TestCase;
use TextHyphenation\Database\QueryBuilder;

class QueryBuilderTest extends TestCase
{
    /**
     * @return QueryBuilder
     * @throws \Exception
     */
    public function testSelect(): QueryBuilder
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder->select(['column1', 'column2'])->from(['table1']);
        $this->assertEquals('SELECT column1, column2 FROM table1', $queryBuilder->getQuery());

        return $queryBuilder;
    }

    /**
     * @throws \Exception
     */
    public function testDelete(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder->delete()->from(['table1']);
        $this->assertEquals('DELETE FROM table1', $queryBuilder->getQuery());
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @throws \Exception
     *
     * @depends testSelect
     */
    public function testWhere(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->where('column1', [':column1' => 'value1']);

        $this->assertEquals(
            'SELECT column1, column2 FROM table1 WHERE column1 = :column1',
            $queryBuilder->getQuery()
        );

        $queryBuilder->andWhere('column2', [':column2' => 'value2']);

        $this->assertEquals(
            'SELECT column1, column2 FROM table1 WHERE column1 = :column1 AND column2 = :column2',
            $queryBuilder->getQuery()
        );

        $queryBuilder->andWhere('column3', 'value3');
        $this->assertEquals(
            'SELECT column1, column2 FROM table1 WHERE column1 = :column1 AND column2 = :column2 AND column3 = value3',
            $queryBuilder->getQuery()
        );
    }

    /**
     * @param string $table
     * @param array $columns
     * @param array $params
     * @throws \Exception
     *
     * @dataProvider insertAndUpdateProvider
     */
    public function testInsert(string $table, array $columns, array $params): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder->insert($table, $columns, $params);

        $columns = implode(', ', $columns);
        $values = implode(', ', array_keys($params));
        $this->assertEquals(
            "INSERT INTO $table($columns) VALUES($values)",
            $queryBuilder->getQuery()
        );
    }

    /**
     * @param string $table
     * @param array $columns
     * @param array $params
     * @throws \Exception
     *
     * @dataProvider insertAndUpdateProvider
     */
    public function testUpdate(string $table, array $columns, array $params): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder->update($table, $columns, $params);

        $colsValues = implode(', ', array_map(function (string $column, string $value) {
            return $column . ' = ' . $value;
        }, $columns, array_keys($params)));

        $this->assertEquals("UPDATE $table SET $colsValues", $queryBuilder->getQuery());
    }

    /**
     * @return array
     */
    public function insertAndUpdateProvider(): array
    {
        return [
            [
                'table',
                ['column1', 'column2'],
                [':column1' => 'value1', ':column2' => 'value1']
            ],
            [
                'table',
                ['column1'],
                [':column1' => 'value1']
            ],
        ];
    }
}