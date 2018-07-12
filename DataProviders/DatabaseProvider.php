<?php

namespace TextHyphenation\DataProviders;


use PDO;
use TextHyphenation\Database\Database;
use TextHyphenation\Database\QueryBuilder;

class DatabaseProvider extends Database
{
    public function __construct()
    {
        parent::__construct();
        $this->createDatabase('hyphenation');
//        $this->dropTables();
        $this->createTables();
    }

    /**
     * @param array $patterns
     * @return bool
     */
    public function importPatterns(array $patterns): bool
    {
        $this->beginTransaction();
        $queryBuilder = new QueryBuilder();
        $queryBuilder->delete()->from(['patterns']);
        $this
            ->prepare($queryBuilder->getQuery())
            ->execute($queryBuilder->getParams());

        $queryBuilder
            ->reset()
            ->insert('patterns', ['pattern'], [':pattern' => '']);
        $this->prepare($queryBuilder->getQuery());

        foreach ($patterns as $pattern) {
            $queryBuilder->addParamValue(':pattern', $pattern);
            $this->execute($queryBuilder->getParams());
        }
        return $this->commit();
    }

    /**
     * @param string $word
     * @param string $hyphenated
     * @param array $usedPatterns
     * @return bool
     */
    public function insertWord(string $word, string $hyphenated, array $usedPatterns) : bool
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder
            ->reset()
            ->select(['id'])->from(['patterns'])
            ->where('pattern', [':pattern' => '']);

        $this->prepare($queryBuilder->getQuery());
        $patternsIds = [];
        foreach ($usedPatterns as $pattern) {
            $queryBuilder->addParamValue(':pattern', $pattern);
            $patternsIds[] = array_column($this->execute($queryBuilder->getParams()), 'id')[0];
        }

        $this->beginTransaction();

        $queryBuilder->reset()->insert('words', ['word', 'hyphenated'], [
            ':word' => $word,
            ':hyphenated' => $hyphenated
        ]);

        $this
            ->prepare($queryBuilder->getQuery())
            ->execute($queryBuilder->getParams());

        $queryBuilder
            ->reset()
            ->select(['*'])->from(['words'])
            ->where('word', [':word' => $word]);

        $word = $this
            ->prepare($queryBuilder->getQuery())
            ->execute($queryBuilder->getParams())[0]['id'];
        $queryBuilder
            ->reset()
            ->insert('patterns_words', ['pattern_id', 'word_id'], [
                ':pattern' => '',
                ':word' => $word
            ]);
        $this->prepare($queryBuilder->getQuery());
        foreach ($patternsIds as $id) {
            $queryBuilder->addParamValue(':pattern', $id);
            $this->execute($queryBuilder->getParams());
        }

        return $this->commit();
    }

    /**
     * @param string $word
     * @return array|null
     */
    public function searchWord(string $word) : ?array
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder
            ->select(['*'])->from(['words'])
            ->where('word', [':word' => $word]);
        $result = $this
            ->prepare($queryBuilder->getQuery())
            ->execute($queryBuilder->getParams());

        if (!empty($result)) {
            $result = $result[0];
            $queryBuilder
                ->reset()
                ->select(['pattern'])->from(['patterns', 'patterns_words'])
                ->where('pattern_id', 'id')
                ->andWhere('word_id', $result['id']);
            $result['patterns'] = array_column(
                $this
                    ->prepare($queryBuilder->getQuery())
                    ->execute($queryBuilder->getParams()),
                'pattern'
            );

            return $result;
        }

        return null;
    }

    private function createTables() : void
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

    private function dropTables() : void
    {
        $this->dropTable('patterns_words');
        $this->dropTable('patterns');
        $this->dropTable('words');
    }
}