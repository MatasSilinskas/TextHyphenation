<?php

namespace TextHyphenation\Hyphenators;

use TextHyphenation\Cache\CacheInterface;
use TextHyphenation\Logger\LoggerInterface;

class CachingHyphenator extends Hyphenator implements HyphenatorInterface
{
    private $cache;
    /**
     * CachingHyphenator constructor.
     * @param array $patterns
     * @param LoggerInterface $logger
     * @param CacheInterface $cache
     */
    public function __construct(array $patterns, LoggerInterface $logger, CacheInterface $cache)
    {
        parent::__construct($patterns, $logger);
        $this->cache = $cache;
    }

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
