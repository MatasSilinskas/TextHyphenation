<?php

namespace TextHyphenation\DataProviders;

class ProviderFactory
{
    const PATTERNS_FILENAME = './src/Data/text-hyphenation-patterns.txt';
    const WORDS_FILENAME = './src/Data/words.txt';

    /**
     * @param string $dataType
     * @return FileProvider
     * @throws \Exception
     */
    public function createProvider(string $dataType): DataProvider
    {
        $dataType = strtolower($dataType);
        if ($dataType === 'patterns') {
            return new FileProvider(self::PATTERNS_FILENAME);
        } elseif ($dataType === 'words') {
            return new FileProvider(self::WORDS_FILENAME);
        }

        throw new \Exception("$dataType data type doesn`t exist!");
    }
}
