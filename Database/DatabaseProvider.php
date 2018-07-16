<?php

namespace TextHyphenation\Database;

class DatabaseProvider extends Database
{
    private $queryBuilder;

    public function __construct(string $dsn, string $username, string $password)
    {
        parent::__construct($dsn, $username, $password);
        $this->createDatabase('hyphenation');
//        $this->dropTables();
        $this->createTables();
        $this->queryBuilder = new QueryBuilder();
    }

    /**
     * @param array $patterns
     * @return bool
     */
    public function importPatterns(array $patterns): bool
    {
        $this->beginTransaction();
        $this->deleteOldPatterns();
        $this->insertNewPatterns($patterns);
        return $this->commit();
    }

    /**
     * @param string $word
     * @param string $hyphenated
     * @param array $usedPatterns
     * @return bool
     */
    public function insertWord(string $word, string $hyphenated, array $usedPatterns): bool
    {
        $patternsIds = $this->getPatternsIds($usedPatterns);
        $this->beginTransaction();
        $this->insertNewWord($word, $hyphenated);
        $word = $this->findWord($word)[0]['id'];
        $this->insertWordAndPatternsRelations($word, $patternsIds);
        return $this->commit();
    }

    /**
     * @param string $word
     * @return array|null
     */
    public function searchWord(string $word): ?array
    {
        $result = $this->findWord($word);
        if (!empty($result)) {
            $result = $result[0];
            $result['patterns'] = $this->findPatterns($result['id']);
            return $result;
        }

        return null;
    }

    public function getWords(): array
    {
        $this->queryBuilder
            ->reset()
            ->select(['*'])->from(['words']);

        return $this->prepareAndExecute();
    }

    /**
     * @param string $word
     * @return int|null
     */
    public function deleteWord(string $word): ?int
    {
        $this->queryBuilder
            ->reset()
            ->delete()->from(['words'])
            ->where('word', [':word' => $word]);

        return $this->checkRows();
    }

    /**
     * @param string $pattern
     */
    public function insertPattern(string $pattern): void
    {
        $this->queryBuilder
            ->reset()
            ->insert('patterns', ['pattern'], [':pattern' => $pattern]);

        $this->prepareAndExecute();
    }

    /**
     * @param array $params
     * @return int|null
     */
    public function updatePattern(array $params): ?int
    {
        $this->queryBuilder
            ->reset()
            ->update('patterns', ['pattern'], [':new' => $params['newPattern']])
            ->where('pattern', [':old' => $params['oldPattern']]);
        return $this->checkRows();
    }

    private function checkRows(): ?int
    {
        if ($this->prepareAndExecute($affectedRows) === false) {
            return null;
        }

        return $affectedRows;
    }

    private function createTables(): void
    {
        $this->createTable('patterns', [
            'id INT(11) AUTO_INCREMENT PRIMARY KEY',
            'pattern VARCHAR(10) UNIQUE NOT NULL'
        ]);
        $this->createTable('words', [
            'id INT(11) AUTO_INCREMENT PRIMARY KEY',
            'word VARCHAR(100) UNIQUE NOT NULL',
            'hyphenated VARCHAR(100) UNIQUE NOT NULL',
        ]);
        $this->createTable('patterns_words', [
            'pattern_id INT(11) NOT NULL',
            'word_id INT(11) NOT NULL',
            'CONSTRAINT pattern_constraint FOREIGN KEY (pattern_id) REFERENCES patterns(id) ON DELETE CASCADE',
            'CONSTRAINT word_constraint FOREIGN KEY (word_id) REFERENCES words(id) ON DELETE CASCADE'
        ]);
    }

    private function dropTables(): void
    {
        $this->dropTable('patterns_words');
        $this->dropTable('patterns');
        $this->dropTable('words');
    }

    /**
     * @param int $affectedRows
     * @return array
     */
    private function prepareAndExecute(&$affectedRows = 0): array
    {
        $result = $this
            ->prepare($this->queryBuilder->getQuery())
            ->execute($this->queryBuilder->getParams(),$affectedRows);

        $this->queryBuilder->reset();
        return $result;
    }

    private function deleteOldPatterns(): void
    {
        $this->queryBuilder->reset()->delete()->from(['patterns']);
        $this->prepareAndExecute();
    }

    /**
     * @param array $patterns
     */
    private function insertNewPatterns(array $patterns): void
    {
        $this->queryBuilder
            ->reset()
            ->insert('patterns', ['pattern'], [':pattern' => '']);
        $this->prepare($this->queryBuilder->getQuery());

        foreach ($patterns as $pattern) {
            $this->queryBuilder->addParamValue(':pattern', $pattern);
            $this->execute($this->queryBuilder->getParams());
        }
    }

    /**
     * @param array $patterns
     * @return array
     */
    private function getPatternsIds(array $patterns): array
    {
        $this->queryBuilder
            ->reset()
            ->select(['id'])->from(['patterns'])
            ->where('pattern', [':pattern' => '']);

        $this->prepare($this->queryBuilder->getQuery());
        $patternsIds = [];
        foreach ($patterns as $pattern) {
            $this->queryBuilder->addParamValue(':pattern', $pattern);
            $patternsIds[] = array_column($this->execute($this->queryBuilder->getParams()), 'id')[0];
        }

        return $patternsIds;
    }

    /**
     * @param string $word
     * @param string $hyphenated
     */
    private function insertNewWord(string $word, string $hyphenated)
    {
        $this->queryBuilder->reset()->insert('words', ['word', 'hyphenated'], [
            ':word' => $word,
            ':hyphenated' => $hyphenated
        ]);

        $this->prepareAndExecute();
    }

    /**
     * @param string $word
     * @return array
     */
    private function findWord(string $word): array
    {
        $this->queryBuilder
            ->reset()
            ->select(['*'])->from(['words'])
            ->where('word', [':word' => $word]);
        return $this->prepareAndExecute();
    }

    /**
     * @param string $word
     * @param array $patternsIds
     */
    private function insertWordAndPatternsRelations(string $word, array $patternsIds): void
    {
        $this->queryBuilder
            ->reset()
            ->insert('patterns_words', ['pattern_id', 'word_id'], [
                ':pattern' => '',
                ':word' => $word
            ]);
        $this->prepare($this->queryBuilder->getQuery());
        foreach ($patternsIds as $id) {
            $this->queryBuilder->addParamValue(':pattern', $id);
            $this->execute($this->queryBuilder->getParams());
        }
    }

    /**
     * @param int $wordId
     * @return array
     */
    private function findPatterns(int $wordId): array
    {
        $this->queryBuilder
            ->reset()
            ->select(['pattern'])->from(['patterns', 'patterns_words'])
            ->where('pattern_id', 'id')
            ->andWhere('word_id', $wordId);

        return array_column($this->prepareAndExecute(), 'pattern');
    }
}
