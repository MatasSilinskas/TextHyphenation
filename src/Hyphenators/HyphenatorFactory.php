<?php

namespace TextHyphenation\Hyphenators;

class HyphenatorFactory
{
    private $patterns;

    private const NORMAL_HYPHERNATOR = 'normal';
    private const REGEX_HYPHERNATOR = 'regex';
    private const PROXY_HYPHERNATOR = 'proxy';
    /**
     * HyphenatorFactory constructor.
     * @param $patterns
     */
    public function __construct(array $patterns)
    {
        $this->patterns = $patterns;
    }

    /**
     * @param string $option
     * @return HyphenatorInterface
     * @throws NotImplementedException
     */
    public function createHyphenator(string $option = self::NORMAL_HYPHERNATOR): HyphenatorInterface
    {
        if ($option === self::REGEX_HYPHERNATOR) {
            return new RegexHyphenator($this->patterns);
        } elseif ($option === self::PROXY_HYPHERNATOR) {
            return new ProxyHyphenator($this->patterns);
        } elseif ($option === self::NORMAL_HYPHERNATOR) {
            return new Hyphenator($this->patterns);
        }

        throw new NotImplementedException();
    }
}
