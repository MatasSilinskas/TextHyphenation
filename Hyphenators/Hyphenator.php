<?php

namespace Hyphenators;

use DataProviders\PatternsProvider;

class Hyphenator implements HyphenatorInterface
{
    private $patterns;
    private const NUMBERS = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    /**
     * Hyphenator constructor.
     * @param PatternsProvider $patternsProvider
     */
    public function __construct(PatternsProvider $patternsProvider)
    {
        $this->patterns = $patternsProvider->getData();
    }

    /**
     * @param string $word
     * @return string
     */
    public function hyphenate(string $word) : string
    {
        $data = [];
        foreach ($this->patterns as $pattern) {
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
                } elseif (substr($word, strlen($word) - strlen($needle) + 1, strlen($word)) ===
                    substr($needle, 0, -1)
                ) {
                    foreach ($matches as $number) {
//                        echo $pattern . "\n";
                        $numPos = strlen($word) - strpos($pattern, $number);
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
        unset($data[0]);
        unset($data[strlen($word) - 1]);
//        print_r($data);
        foreach ($data as $key => $value) {
            if ($value % 2 !== 0) {
                $word = substr_replace($word, '-', $key, 0);
            }
        }

        return $word;
    }
}
