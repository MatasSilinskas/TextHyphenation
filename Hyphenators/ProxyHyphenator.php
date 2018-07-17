<?php

namespace TextHyphenation\Hyphenators;


class ProxyHyphenator implements HyphenatorInterface
{
    /**
     * @var Hyphenator $hyphenator
     */
    private $hyphenator;
    private $patterns;

    /**
     * ProxyHyphenator constructor.
     * @param array $patterns
     */
    public function __construct(array $patterns)
    {
        $this->patterns = $patterns;
    }


    /**
     * @param string $word
     * @param array $usedPatterns
     * @return string
     */
    public function hyphenate(string $word, array &$usedPatterns = []): string
    {
        $this->lazyLoad();
        return $this->hyphenator->hyphenate($word, $usedPatterns);
    }

    private function lazyLoad()
    {
        if ($this->hyphenator === NULL) {
            $this->hyphenator = new Hyphenator($this->patterns);
        }
    }
}