<?php

namespace TextHyphenation\Hyphenators;

class RegexHyphenator extends Hyphenator
{
    private const SEARCH_FOR = [
        '#^[.]#',
        '#(\d+)#',
        '#[.]$#',
    ];

    /**
     * RegexHyphenator constructor.
     * @param array $patterns
     */
    public function __construct(array $patterns)
    {
        parent::__construct($patterns);
    }

    /**
     * @param string $pattern
     * @return string
     */
    protected function normalizePattern(string $pattern): string
    {
        return preg_replace(self::SEARCH_FOR, '', $pattern);
    }

    /**
     * @param array $data
     * @param string $word
     * @param string $pattern
     */
    protected function putHyphenPosition(array &$data, string $word, string $pattern): void
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
