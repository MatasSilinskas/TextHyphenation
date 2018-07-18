<?php

namespace TextHyphenation\Executables;

interface FacadeInterface
{
    /**
     * @param string $sentence
     * @return array
     */
    public function hyphenate(string $sentence): array;

    /**
     * @param array $sentences
     * @return array
     */
    public function hyphenateMany(array $sentences): array;

    /**
     * @param bool $databaseUsage
     */
    public function setDatabaseUsage(bool $databaseUsage): void;

    public function importPatterns(): void;
}
