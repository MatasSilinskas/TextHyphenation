<?php

namespace TextHyphenation\Database;


class WordsRepository extends Repository
{
    /**
     * @param string $word
     * @param string $hyphenated
     * @param array $usedPatterns
     * @return bool
     */
    public function insertWord(string $word, string $hyphenated, array $usedPatterns): bool
    {
        $patternsIds = $this->getPatternsIds($usedPatterns);
        $this->database->beginTransaction();
        $this->insertNewWord($word, $hyphenated);
        $word = $this->findWord($word)[0]['id'];
        $this->insertWordAndPatternsRelations($word, $patternsIds);
        return $this->database->commit();
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

    /**
     * @return array
     */
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

    public function getWord(int $id): array
    {
        $this->queryBuilder
            ->reset()
            ->select(['*'])->from(['words'])
            ->where('id', [':id' => $id]);
        return $this->prepareAndExecute();
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
        $this->database->prepare($this->queryBuilder->getQuery());
        foreach ($patternsIds as $id) {
            $this->queryBuilder->addParamValue(':pattern', $id);
            $this->database->execute($this->queryBuilder->getParams());
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

        $this->database->prepare($this->queryBuilder->getQuery());
        $patternsIds = [];
        foreach ($patterns as $pattern) {
            $this->queryBuilder->addParamValue(':pattern', $pattern);
            $patternsIds[] = array_column($this->database->execute($this->queryBuilder->getParams()), 'id')[0];
        }

        return $patternsIds;
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