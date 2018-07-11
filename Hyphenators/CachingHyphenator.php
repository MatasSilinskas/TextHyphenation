<?php

namespace TextHyphenation\Hyphenators;

use TextHyphenation\Cache\CacheInterface;
use TextHyphenation\Cache\InvalidArgumentException;

class CachingHyphenator extends Hyphenator implements HyphenatorInterface
{
    private $cache;

    /**
     * CachingHyphenator constructor.
     * @param array $patterns
     * @param CacheInterface $cache
     */
    public function __construct(array $patterns, CacheInterface $cache)
    {
        parent::__construct($patterns);
        $this->cache = $cache;
    }

    /**
     * @param string $word
     * @return string
     * @throws InvalidArgumentException
     */
    public function hyphenate(string $word): string
    {
        if ($this->cache->has($word)) {
            return $this->cache->get($word);
        }

        $hyphenated = parent::hyphenate($word);
        $this->cache->set($word, $hyphenated);
        return $hyphenated;
    }
}
