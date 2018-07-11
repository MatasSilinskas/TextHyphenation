<?php

namespace TextHyphenation\Console;


interface ToolsInterface
{
    public function modify(string $sentence): array;
    public function modifyMany(array $sentences): array;
    public function setUseDatabase(bool $useDatabase): void;
    public function importPatterns(): void;
}