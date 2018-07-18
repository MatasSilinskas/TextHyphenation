<?php

namespace TextHyphenation\DataProviders;

class PatternsProvider extends FileProvider
{
    const FILENAME = './src/Data/text-hyphenation-patterns.txt';
    /**
     * PatternsProvider constructor.
     */
    public function __construct()
    {
        parent::__construct(self::FILENAME);
    }
}
