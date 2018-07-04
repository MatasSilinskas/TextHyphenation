<?php

namespace Hyphenators;

use DataProviders\PatternsProvider;

class NewHyphenator implements HyphenatorInterface
{
    private $patterns = [];
    const NUMBERS = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
    private static $numberOfInstances = 0;

    /**
     * Hyphenator constructor.
     * @param PatternsProvider $patternsProvider
     */
    public function __construct(PatternsProvider $patternsProvider)
    {
        $adjacent = array_merge(self::NUMBERS, ['.']);
        foreach ($patternsProvider->getData() as $pattern) {
            $this->patterns[str_replace($adjacent, '', $pattern)[0]][str_replace($adjacent, '', $pattern)[1]][] = $pattern;
        }
        self::$numberOfInstances++;
    }

    /**
     * @param string $word
     * @return string
     */
    public function hyphenate(string $word) : string
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
        if (!empty($filteredPatterns)) {
            $data = [];
            foreach (array_merge(...$filteredPatterns) as $pattern) {
                $needle = str_replace(self::NUMBERS, '', $pattern);
                preg_match('#\d+#', $pattern, $matches);

                if (($dotPos = strpos($needle, '.')) !== false) {
                    //dot at the beginning
                    if (($dotPos === 0 && substr($word, 0, strlen($needle) - 1) === substr($needle, 1))) {
                        foreach ($matches as $number) {
//                        echo $pattern . "\n";
                            $numPos = strpos($pattern, $number);
                            if (!isset($data[$numPos - 1]) || $data[$numPos - 1] < $number) {
                                $data[$numPos - 1] = $number;
                            }
                        }
                    } //dot at the end
                    elseif (substr($word, strlen($word) - strlen($needle) + 1, strlen($word)) === substr($needle, 0,
                            -1)) {
                        foreach ($matches as $number) {
                            $numPos = strlen($pattern) - strpos($pattern, $number) - 2;
                            if (!isset($data[strlen($word) - $numPos]) || $data[strlen($word) - $numPos] < $number) {
                                $data[strlen($word) - $numPos] = $number;
                            }
                        }
                    }
                } else {
                    $lastPos = 0;
                    while (($lastPos = strpos($word, $needle, $lastPos)) !== false) {
                        foreach ($matches as $number) {
//                        echo $pattern . "\n";
                            $numPos = strpos($pattern, $number);
                            if (!isset($data[$lastPos + $numPos]) || $data[$lastPos + $numPos] < $number) {
                                $data[$lastPos + $numPos] = $number;
                            }
                        }
                        $lastPos = $lastPos + strlen($needle);
                    }
                }
            }

            krsort($data);
//        print_r($data);
            foreach ($data as $key => $value) {
                if ($value % 2 !== 0) {
                    $word = substr_replace($word, '-', $key, 0);
                }
            }
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
}