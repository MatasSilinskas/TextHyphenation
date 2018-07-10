<?php

namespace TextHyphenation\Hyphenators;

use TextHyphenation\Logger\LoggerInterface;

class Hyphenator implements HyphenatorInterface
{
    private $patterns = [];
    private $logger;
    private const SEARCH_FOR = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, '.'];
    private static $numberOfInstances = 0;

    /**
     * Hyphenator constructor.
     * @param array $patterns
     * @param LoggerInterface $logger
     */
    public function __construct(array $patterns, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->constructPatterns($patterns);
        self::$numberOfInstances++;
    }

    /**
     * @param string $sentence
     * @return string
     */
    public function hyphenate(string $sentence) : string
    {
        $words = array_filter(preg_split('#(\W+)#', $sentence));
        $separators = preg_split('#(\w+)#', $sentence);
        array_shift($separators);
        $hyphenatedSentence = '';
        foreach ($words as $word) {
            $filteredPatterns = $this->filterPatterns($word);
            $hyphenPositions = [];
            foreach ($filteredPatterns as $pattern) {
                $this->putHyphenPosition($hyphenPositions, $word, $pattern);
            }

            $hyphenated = $this->addHyphens($hyphenPositions, $word);

            if ($hyphenated === $word) {
                $this->logger->warning("Hyhphenated form of the word $word is equal to it`s original form.");
            }

            $hyphenatedSentence .= $hyphenated . array_shift($separators);
        }
        return $hyphenatedSentence;
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
    public static function setNumberOfInstances(int $numberOfInstances) : void
    {
        self::$numberOfInstances = $numberOfInstances;
    }

    /**
     * @param array $patterns
     */
    protected function constructPatterns(array $patterns) : void
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
    private function filterPatterns(string $word) : array
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
    protected function putHyphenPosition(array &$data, string $word, string $pattern) : void
    {
        $needle = $this->normalizePattern($pattern);
        preg_match('#\d+#', $pattern, $numbers);

        if ($dotPos = strpos($pattern, '.')) {
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
    protected function putHyphenPositionForStartDotPattern(array $numbers, string $pattern, array &$data) : void
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
    protected function putHyphenPositionForEndDotPattern(
        array $numbers,
        string $pattern,
        array &$data,
        string $word
    ) : void {
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
    protected function putHyphenPositionForNormalPattern(
        array $numbers,
        string $pattern,
        array &$data,
        string $word,
        string $needle
    ) : void {
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
    private function addHyphens(array $hyphenPositions, string $word) : string
    {
        krsort($hyphenPositions);
        unset($hyphenPositions[0]);
        unset($hyphenPositions[strlen($word)]);
        foreach ($hyphenPositions as $key => $value) {
            if ($value % 2 !== 0) {
                $word = substr_replace($word, '-', $key, 0);
            }
        }

        return $word;
    }

    protected function normalizePattern(string $pattern) : string
    {
        return str_replace(self::SEARCH_FOR, '', $pattern);
    }
}
