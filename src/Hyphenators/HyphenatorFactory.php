<?php

namespace TextHyphenation\Hyphenators;

class HyphenatorFactory
{
    private $patterns;

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
    public function createHyphenator(string $option = 'normal'): HyphenatorInterface
    {
        if ($option === 'regex') {
            return new RegexHyphenator($this->patterns);
        } elseif ($option === 'proxy') {
            return new ProxyHyphenator($this->patterns);
        } elseif ($option === 'normal') {
            return new Hyphenator($this->patterns);
        }

        throw new NotImplementedException();
    }
}
