<?php

namespace TextHyphenation\Hyphenators;

abstract class HyphenatorDecorator implements HyphenatorInterface
{
    private $hyphenator;

    /**
     * HyphenatorDecorator constructor.
     * @param $hyphenator
     */
    public function __construct(HyphenatorInterface $hyphenator)
    {
        $this->hyphenator = $hyphenator;
    }

    /**
     * @return HyphenatorInterface
     */
    public function getHyphenator(): HyphenatorInterface
    {
        return $this->hyphenator;
    }

    /**
     * @param HyphenatorInterface $hyphenator
     */
    public function setHyphenator(HyphenatorInterface $hyphenator): void
    {
        $this->hyphenator = $hyphenator;
    }
}
