<?php

namespace TextHyphenation\Hyphenators;

class Hyphenator implements HyphenatorInterface
{
    private $patterns = [];
    private const SEARCH_FOR = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, '.'];

    /**
     * Hyphenator constructor.
     * @param array $patterns
     */
    public function __construct(array $patterns)
    {
        $this->constructPatterns($patterns);
    }

    /**
     * @param string $word
     * @param array $usedPatterns
     * @return string
     */
    public function hyphenate(string $word, array &$usedPatterns = []): string
    {
        $filteredPatterns = $this->filterPatterns($word);
        $hyphenPositions = [];
        foreach ($filteredPatterns as $pattern) {
            $this->putHyphenPosition($hyphenPositions, $word, $pattern);
        }
        return $this->addHyphens($hyphenPositions, $word, $usedPatterns);
    }

    /**
     * @param array $patterns
     */
    protected function constructPatterns(array $patterns): void
    {
        foreach ($patterns as $pattern) {
            $letters = $this->normalizePattern($pattern);
            $this->patterns[$letters[0]][$letters[1]][] = $pattern;
        }
    }

    /**
     * @param string $word
     * @return array
     */
    private function filterPatterns(string $word): array
    {
        $letters = str_split($word);
        $filteredPatterns = [];
        for ($i = 0; $i < count($letters) - 1; $i++) {
            if (isset($this->patterns[$letters[$i]][$letters[$i + 1]]) &&
                !in_array($this->patterns[$letters[$i]][$letters[$i + 1]], $filteredPatterns)
            ) {
                $filteredPatterns[] = $this->patterns[$letters[$i]][$letters[$i + 1]];
            }
        }

        if (!empty($filteredPatterns)) {
            return array_merge(...$filteredPatterns);
        }

        return [];
    }

    /**
     * @param array $data
     * @param string $word
     * @param string $pattern
     */
    protected function putHyphenPosition(array &$data, string $word, string $pattern): void
    {
        $needle = $this->normalizePattern($pattern);
        preg_match_all('#\d+#', $pattern, $numbers);
        $numbers = array_shift($numbers);

        if (($dotPos = strpos($pattern, '.')) !== false) {
            if ($dotPos === 0 && substr($word, 0, strlen($needle)) === $needle) {
                $this->putHyphenPositionForStartDotPattern($numbers, $pattern, $data);
            } elseif (substr($word, strlen($word) - strlen($needle)) === $needle) {
                $this->putHyphenPositionForEndDotPattern($numbers, $pattern, $data, $word);
            }

            return;
        }

        $this->putHyphenPositionForNormalPattern($numbers, $pattern, $data, $word, $needle);
    }

    /**
     * @param array $numbers
     * @param string $pattern
     * @param array $data
     */
    protected function putHyphenPositionForStartDotPattern(array $numbers, string $pattern, array &$data): void
    {
        foreach ($numbers as $number) {
            $numPos = strpos($pattern, $number);
            if (!isset($data[$numPos - 1]) || $data[$numPos - 1][0] < $number) {
                $data[$numPos - 1] = [$number, $pattern];
            }
        }
    }

    /**
     * @param array $numbers
     * @param string $pattern
     * @param array $data
     * @param string $word
     */
    protected function putHyphenPositionForEndDotPattern(
        array $numbers,
        string $pattern,
        array &$data,
        string $word
    ): void {
        foreach ($numbers as $number) {
            $numPos = strlen($pattern) - strpos($pattern, $number) - 2;
//            echo $pattern . "\n";
            if (!isset($data[strlen($word) - $numPos]) || $data[strlen($word) - $numPos][0] < $number) {
                $data[strlen($word) - $numPos] = [$number, $pattern];
            }
        }
    }

    /**
     * @param array $numbers
     * @param string $pattern
     * @param array $data
     * @param string $word
     * @param string $needle
     */
    protected function putHyphenPositionForNormalPattern(
        array $numbers,
        string $pattern,
        array &$data,
        string $word,
        string $needle
    ): void {
        $lastPos = 0;
        $unchangedPattern = $pattern;
        while (($lastPos = strpos($word, $needle, $lastPos)) !== false) {
            foreach ($numbers as $number) {
                $numPos = strpos($pattern, $number);
                if (!isset($data[$lastPos + $numPos]) || $data[$lastPos + $numPos][0] < $number) {
                    $data[$lastPos + $numPos] = [$number, $unchangedPattern];
                }
                $pattern = substr_replace($pattern, '', $numPos, strlen($number));
            }
            $lastPos += strlen($needle);
        }
    }

    /**
     * @param array $hyphenPositions
     * @param string $word
     * @return string
     */
    private function addHyphens(array $hyphenPositions, string $word, array &$usedPatterns): string
    {
        krsort($hyphenPositions);
        unset($hyphenPositions[0]);
        unset($hyphenPositions[strlen($word)]);

        $usedPatterns = [];
        foreach ($hyphenPositions as $key => $value) {
            if ($value[0] % 2 !== 0) {
                $word = substr_replace($word, '-', $key, 0);
                if (!in_array($value[1], $usedPatterns)) {
                    $usedPatterns[] = $value[1];
                }
            }
        }

        return $word;
    }

    protected function normalizePattern(string $pattern): string
    {
        return str_replace(self::SEARCH_FOR, '', $pattern);
    }
}
