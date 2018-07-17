<?php

namespace TextHyphenation\Hyphenators;

use TextHyphenation\Cache\CacheInterface;

class Cache extends HyphenatorDecorator
{
    private $cache;

    public function __construct(HyphenatorInterface $hyphenator, CacheInterface $cache)
    {
        parent::__construct($hyphenator);
        $this->cache = $cache;
    }

    /**
     * @param string $word
     * @param array $usedPatterns
     * @return string
     * @throws \TextHyphenation\Cache\InvalidArgumentException
     */
    public function hyphenate(string $word, array &$usedPatterns = []): string
    {
        if ($this->cache->has($word)) {
            return $this->cache->get($word);
        }

        $hyphenated = $this->getHyphenator()->hyphenate($word, $usedPatterns);
        $this->cache->set($word, $hyphenated);
        return $hyphenated;
    }
}