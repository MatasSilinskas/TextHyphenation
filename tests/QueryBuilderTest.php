<?php

namespace TextHyphenation\Tests;

use PHPUnit\Framework\TestCase;
use TextHyphenation\Database\QueryBuilder;

class QueryBuilderTest extends TestCase
{
    /**
     * @var QueryBuilder $queryBuilder
     */
    private $queryBuilder;

    protected function setUp(): void
    {
        $this->queryBuilder = new QueryBuilder();
    }

    /**
     * @return QueryBuilder
     * @throws \Exception
     */
    public function testSelect(): QueryBuilder
    {
        $this->queryBuilder->select(['column1', 'column2'])->from(['table1']);
        $this->assertSame('SELECT column1, column2 FROM table1', $this->queryBuilder->getQuery());

        return $this->queryBuilder;
    }

    /**
     * @throws \Exception
     */
    public function testDelete(): void
    {
        $this->queryBuilder->delete()->from(['table1']);
        $this->assertSame('DELETE FROM table1', $this->queryBuilder->getQuery());
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

        $this->assertSame(
            'SELECT column1, column2 FROM table1 WHERE column1 = :column1',
            $queryBuilder->getQuery()
        );

        $queryBuilder->andWhere('column2', [':column2' => 'value2']);

        $this->assertSame(
            'SELECT column1, column2 FROM table1 WHERE column1 = :column1 AND column2 = :column2',
            $queryBuilder->getQuery()
        );

        $queryBuilder->andWhere('column3', 'value3');
        $this->assertSame(
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
        $this->queryBuilder->insert($table, $columns, $params);

        $columns = implode(', ', $columns);
        $values = implode(', ', array_keys($params));
        $this->assertSame(
            "INSERT INTO $table($columns) VALUES($values)",
            $this->queryBuilder->getQuery()
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
        $this->queryBuilder->update($table, $columns, $params);

        $colsValues = implode(', ', array_map(function (string $column, string $value) {
            return $column . ' = ' . $value;
        }, $columns, array_keys($params)));

        $this->assertSame("UPDATE $table SET $colsValues", $this->queryBuilder->getQuery());
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