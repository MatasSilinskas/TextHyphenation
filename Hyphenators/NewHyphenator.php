<?php

namespace Hyphenators;

class NewHyphenator implements HyphenatorInterface
{
    private $patterns = [];
    const NUMBERS = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
    private static $numberOfInstances = 0;

    /**
     * NewHyphenator constructor.
     * @param array $patterns
     */
    public function __construct(array $patterns)
    {
        $this->constructPatterns($patterns);
        self::$numberOfInstances++;
    }

    /**
     * @param string $word
     * @return string
     */
    public function hyphenate(string $word) : string
    {
        $filteredPatterns = $this->filterPatterns($word);
        if (!empty($filteredPatterns)) {
            $hyphenPositions = [];
            foreach (array_merge(...$filteredPatterns) as $pattern) {
                $this->putHyphenPosition($hyphenPositions, $word, $pattern);
            }

            $word = $this->addHyphens($hyphenPositions, $word);
        }

        return $word;
    }

    /**
     * @return int
     */
    public static function getNumberOfInstances(): int
    {
        return self::$numberOfInstances;
    }

    /**
     * @param int $numberOfInstances
     */
    public static function setNumberOfInstances(int $numberOfInstances): void
    {
        self::$numberOfInstances = $numberOfInstances;
    }

    /**
     * @param array $patterns
     */
    private function constructPatterns(array $patterns)
    {
        $adjacent = array_merge(self::NUMBERS, ['.']);
        foreach ($patterns as $pattern) {
            $this->patterns[str_replace($adjacent, '', $pattern)[0]][str_replace($adjacent, '', $pattern)[1]][] = $pattern;
        }
    }

    /**
     * @param string $word
     * @return array
     */
    private function filterPatterns(string $word) : array
    {
        $letters = str_split($word);
        $patterns = array_intersect_key($this->patterns, array_flip(array_unique($letters)));
        $filteredPatterns = [];
        for ($i = 0; $i < count($letters) - 1; $i++) {
            if (isset($patterns[$letters[$i]][$letters[$i + 1]])) {
                $filteredPatterns[] = $patterns[$letters[$i]][$letters[$i + 1]];
                unset($patterns[$letters[$i]][$letters[$i + 1]]);
            }
        }

        return $filteredPatterns;
    }

    /**
     * @param array $data
     * @param string $word
     * @param string $pattern
     */
    private function putHyphenPosition(array &$data, string $word, string $pattern)
    {
        $needle = str_replace(self::NUMBERS, '', $pattern);
        preg_match('#\d+#', $pattern, $numbers);

        if (($dotPos = strpos($needle, '.')) !== false) {
            if (($dotPos === 0 && substr($word, 0, strlen($needle) - 1) ===
                substr($needle, 1))
            ) {
                $this->putHyphenPositionForStartDotPattern($numbers, $pattern, $data);
            }
            elseif (substr($word, strlen($word) - strlen($needle) + 1, strlen($word)) ===
                substr($needle, 0, -1)
            ) {
                $this->putHyphenPositionForEndDotPattern($numbers, $pattern, $data, $word);
            }
        } else {
            $this->putHyphenPositionForNormalPattern($numbers, $pattern, $data, $word, $needle);
        }
    }

    /**
     * @param array $numbers
     * @param string $pattern
     * @param array $data
     */
    private function putHyphenPositionForStartDotPattern(array $numbers, string $pattern, array &$data)
    {
        foreach ($numbers as $number) {
            $numPos = strpos($pattern, $number);
//            echo $pattern . "\n";
            if (!isset($data[$numPos - 1]) || $data[$numPos - 1] < $number) {
                $data[$numPos - 1] = $number;
            }
        }
    }

    /**
     * @param array $numbers
     * @param string $pattern
     * @param array $data
     * @param string $word
     */
    private function putHyphenPositionForEndDotPattern(array $numbers, string $pattern, array &$data, string $word)
    {
        foreach ($numbers as $number) {
            $numPos = strlen($pattern) - strpos($pattern, $number) - 2;
//            echo $pattern . "\n";
            if (!isset($data[strlen($word) - $numPos]) || $data[strlen($word) - $numPos] < $number) {
                $data[strlen($word) - $numPos] = $number;
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
    private function putHyphenPositionForNormalPattern(array $numbers, string $pattern, array &$data, string $word, string $needle)
    {
        $lastPos = 0;
        while (($lastPos = strpos($word, $needle, $lastPos)) !== false) {
            foreach ($numbers as $number) {
//                echo $pattern . "\n";
                $numPos = strpos($pattern, $number);
                if (!isset($data[$lastPos + $numPos]) || $data[$lastPos + $numPos] < $number) {
                    $data[$lastPos + $numPos] = $number;
                }
            }
            $lastPos = $lastPos + strlen($needle);
        }
    }

    /**
     * @param array $hyphenPositions
     * @param string $word
     * @return string
     */
    private function addHyphens(array $hyphenPositions, string $word)
    {
        krsort($hyphenPositions);
        unset($hyphenPositions[0]);
        unset($hyphenPositions[strlen($word) - 1]);
        foreach ($hyphenPositions as $key => $value) {
            if ($value % 2 !== 0) {
                $word = substr_replace($word, '-', $key, 0);
            }
        }

        return $word;
    }
}