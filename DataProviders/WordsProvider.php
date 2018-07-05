<?php

namespace TextHyphenation\DataProviders;

class WordsProvider extends FileProvider
{
    const FILENAME = './Data/words.txt';

    public function __construct()
    {
        parent::__construct(self::FILENAME);
    }
}
