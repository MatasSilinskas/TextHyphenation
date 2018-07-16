<?php

namespace TextHyphenation\Executables;

interface ToolsInterface
{
    public function modify(string $sentence): array;
    public function modifyMany(array $sentences): array;
    public function setUseDatabase(bool $useDatabase): void;
    public function importPatterns(): void;
}
