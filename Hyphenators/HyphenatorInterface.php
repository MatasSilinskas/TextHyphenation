<?php

namespace Hyphenators;


interface HyphenatorInterface
{
    /**
     * @param string $word
     * @return string
     */
    function hyphenate(string $word) : string;
}