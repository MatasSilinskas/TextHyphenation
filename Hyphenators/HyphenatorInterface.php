<?php

namespace Hyphenators;

interface HyphenatorInterface
{
    /**
     * @param string $word
     * @return string
     */
    public function hyphenate(string $word) : string;
}
