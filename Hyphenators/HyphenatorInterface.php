<?php

namespace TextHyphenation\Hyphenators;

interface HyphenatorInterface
{
    /**
     * @param string $word
     * @param array $usedPatterns
     * @return string
     */
    public function hyphenate(string $word, array &$usedPatterns = []) : string;
}
