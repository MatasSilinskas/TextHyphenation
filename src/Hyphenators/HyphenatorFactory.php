<?php

namespace TextHyphenation\Hyphenators;

class HyphenatorFactory
{
    private $patterns;

    private const NORMAL_HYPHENATOR = 'normal';
    private const REGEX_HYPHENATOR = 'regex';
    private const PROXY_HYPHENATOR = 'proxy';
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
    public function createHyphenator(string $option = self::NORMAL_HYPHENATOR): HyphenatorInterface
    {
        if ($option === self::REGEX_HYPHENATOR) {
            return new RegexHyphenator($this->patterns);
        } elseif ($option === self::PROXY_HYPHENATOR) {
            return new ProxyHyphenator($this->patterns);
        } elseif ($option === self::NORMAL_HYPHENATOR) {
            return new Hyphenator($this->patterns);
        }

        throw new NotImplementedException();
    }
}
