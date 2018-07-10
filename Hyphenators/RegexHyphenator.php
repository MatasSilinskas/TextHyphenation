<?php

namespace TextHyphenation\Hyphenators;


use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use TextHyphenation\Logger\LoggerInterface;

class RegexHyphenator extends Hyphenator
{
    private const SEARCH_FOR = [
        '#^[.]#',
        '#(\d+)#',
        '#[.]$#',
    ];

    private $patterns;

    /**
     * RegexHyphenator constructor.
     * @param array $patterns
     * @param LoggerInterface $logger
     */
    public function __construct(array $patterns, LoggerInterface $logger)
    {
        parent::__construct($patterns, $logger);
    }

    protected function normalizePattern(string $pattern) : string
    {
        return preg_replace(self::SEARCH_FOR, '', $pattern);
    }

    protected function putHyphenPosition(array &$data, string $word, string $pattern)
    {
        $needle = $this->normalizePattern($pattern);
        preg_match('#\d+#', $pattern, $numbers);

        if (preg_match('#(?:^[.])(.*)#', $pattern) === 1) {
            if (preg_match('#^' . $needle . '#', $word) === 1) {
                $this->putHyphenPositionForStartDotPattern($numbers, $pattern, $data);
            }
            return;
        } elseif (preg_match('#(.*)(?:[.]$)#', $pattern) === 1) {
            if (preg_match('#' . $needle . '$#', $word) === 1) {
                $this->putHyphenPositionForEndDotPattern($numbers, $pattern, $data, $word);
            }
            return;
        }

        $this->putHyphenPositionForNormalPattern($numbers, $pattern, $data, $word, $needle);
    }
}