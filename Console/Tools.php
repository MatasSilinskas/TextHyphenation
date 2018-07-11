<?php

namespace TextHyphenation\Console;

use TextHyphenation\DataProviders\DatabaseProvider;
use TextHyphenation\DataProviders\PatternsProvider;
use TextHyphenation\Hyphenators\HyphenatorInterface;
use TextHyphenation\Timer\Timer;

class Tools implements ToolsInterface
{
    private $hyphenator;
    private $timer;
    private $database;
    private $useDatabase = false;

    /**
     * Tools constructor.
     * @param HyphenatorInterface $hyphenator
     * @param Timer $timer
     * @param DatabaseProvider $database
     */
    public function __construct(HyphenatorInterface $hyphenator, Timer $timer, DatabaseProvider $database)
    {
        $this->hyphenator = $hyphenator;
        $this->timer = $timer;
        $this->database = $database;
    }

    /**
     * @param string $sentence
     * @return array
     * @throws \Exception
     */
    public function modify(string $sentence) : array
    {
        $this->timer->reset();
        $this->timer->start();
        $result = $this->hyphenateSentence($sentence);
        $this->timer->stop();

        return [
            'time' => $this->timer->getDifference(),
            'result' => $result
        ];
    }

    /**
     * @param array $sentences
     * @return array
     * @throws \Exception
     */
    public function modifyMany(array $sentences) : array
    {
        $this->timer->reset();
        $this->timer->start();
        $result = [];
        foreach ($sentences as $sentence) {
            $result[] = $this->hyphenateSentence($sentence);
        }
        $this->timer->stop();

        return [
            'time' => $this->timer->getDifference(),
            'result' => $result
        ];
    }

    public function importPatterns() : void
    {
        $patternsProvider = new PatternsProvider;
        $this->database->importPatterns($patternsProvider->getData());
    }

    /**
     * @return bool
     */
    public function isUseDatabase(): bool
    {
        return $this->useDatabase;
    }

    /**
     * @param bool $useDatabase
     */
    public function setUseDatabase(bool $useDatabase) : void
    {
        $this->useDatabase = $useDatabase;
    }

    /**
     * @param string $sentence
     * @return string
     */
    private function hyphenateSentence(string $sentence) : string
    {
        $result = '';
        $separators = [];
        $words = $this->splitSentence($sentence, $separators);
        foreach ($words as $word) {
            if ($this->useDatabase) {
                if (($dbWord = $this->database->searchWord($word)) !== null) {
                    $result .= $dbWord['hyphenated'] . array_shift($separators);
                    continue;
                }

                $hyphenated = $this->hyphenator->hyphenate($word);
                $result .= $hyphenated . array_shift($separators);
                $this->database->insertWord($word, $hyphenated);
                continue;
            }

            $result .= $this->hyphenator->hyphenate($word) . array_shift($separators);

        }

        return $result;
    }

    /**
     * @param string $sentence
     * @param array $separators
     * @return array
     */
    private function splitSentence(string $sentence, array &$separators = [])
    {
        $separators = preg_split('#(\w+)#', $sentence);
        array_shift($separators);
        return array_filter(preg_split('#(\W+)#', $sentence));
    }
}