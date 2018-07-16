<?php

namespace TextHyphenation\Executables;

use TextHyphenation\Database\DatabaseProvider;
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
            'result' => $result['hyphenated'],
            'patterns' => $result['patterns'],
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
        $patterns = [];
        foreach ($sentences as $sentence) {
            $hyphenateData = $this->hyphenateSentence($sentence);
            $patterns[$hyphenateData['hyphenated']] = $hyphenateData['patterns'];
        }
        $this->timer->stop();

        return [
            'time' => $this->timer->getDifference(),
            'result' => array_keys($patterns),
            'patterns' => $patterns,
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
     * @return array
     */
    private function hyphenateSentence(string $sentence) : array
    {
        $result = [];
        $result['hyphenated'] = '';
        $result['patterns'] = [];
        $separators = [];
        $words = $this->splitSentence($sentence, $separators);
        foreach ($words as $word) {
            if ($this->useDatabase) {
                if (($dbWord = $this->database->searchWord($word)) !== null) {
                    $result['hyphenated'] .= $dbWord['hyphenated'] . array_shift($separators);
                    $result['patterns'][$word] = $dbWord['patterns'];
                    continue;
                }

                $patterns = [];
                $hyphenated = $this->hyphenator->hyphenate($word, $patterns);
                $result['hyphenated'] .= $hyphenated . array_shift($separators);
                $result['patterns'][$word] = $patterns;
                $this->database->insertWord($word, $hyphenated, $patterns);
                continue;
            }

            $result['hyphenated'] .= $this->hyphenator->hyphenate($word) . array_shift($separators);
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
