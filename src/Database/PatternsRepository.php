<?php

namespace TextHyphenation\Database;

class PatternsRepository extends Repository
{
    /**
     * @param array $patterns
     * @return bool
     */
    public function importPatterns(array $patterns): bool
    {
        $this->database->beginTransaction();
        $this->deleteOldPatterns();
        $this->insertNewPatterns($patterns);
        return $this->database->commit();
    }

    /**
     * @param string $pattern
     * @return int|null
     */
    public function insertPattern(string $pattern): ?int
    {
        $this->queryBuilder
            ->reset()
            ->insert('patterns', ['pattern'], [':pattern' => $pattern]);

        return $this->checkRows();
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

    /**
     * @return array
     */
    public function getPatterns(): array
    {
        $this->queryBuilder
            ->reset()
            ->select(['*'])->from(['patterns']);

        return $this->prepareAndExecute();
    }

    /**
     * @param int $id
     * @return array
     */
    public function getPattern(int $id): array
    {
        $this->queryBuilder
            ->reset()
            ->select(['*'])->from(['patterns'])
            ->where('id', [':id' => $id]);
        return $this->prepareAndExecute();
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
        $this->database->prepare($this->queryBuilder->getQuery());

        foreach ($patterns as $pattern) {
            $this->queryBuilder->addParamValue(':pattern', $pattern);
            $this->database->execute($this->queryBuilder->getParams());
        }
    }

    public function deletePattern(string $pattern)
    {
        $this->queryBuilder
            ->reset()
            ->delete()->from(['patterns'])
            ->where('pattern', [':pattern' => $pattern]);

        return $this->checkRows();
    }
}
