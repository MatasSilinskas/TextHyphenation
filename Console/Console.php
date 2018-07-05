<?php

namespace Console;

use DataProviders\WordsProvider;
use Hyphenators\HyphenatorInterface;
use SplFileObject;
use Timer\Timer;

class Console
{
    private $hyphenator;
    private $timer;

    /**
     * Console constructor.
     * @param HyphenatorInterface $hyphenator
     * @param Timer $timer
     */
    public function __construct(HyphenatorInterface $hyphenator, Timer $timer)
    {
        $this->hyphenator = $hyphenator;
        $this->timer = $timer;
    }

    public function execute()
    {
        echo 'Would you like to hyphenate words from file or enter them yourself?[FILE/yourself]';
        $option = $this->getConsoleInput();
        if ($option === 'yourself') {
            $this->takeInputFromUser();
        } elseif ($option === 'file' || $option === '') {
            $this->takeInputFromFile();
        }
    }

    private function getConsoleInput() : string
    {
        return substr(fgets(STDIN), 0, -1);
    }

    private function takeInputFromUser()
    {
        while (true) {
            echo 'Enter a word you want to hyphenate (or enter :exit to leave): ';
            $input = $this->getConsoleInput();
            if ($input === ':exit') {
                break;
            }

            $this->timer->start();
            $word = $this->hyphenator->hyphenate($input);
            $this->timer->stop();

            echo $word . "\n";
            echo 'The process took ' . $this->timer->getDifference() . " seconds\n";
            $this->timer->reset();
        }
    }

    private function takeInputFromFile()
    {
        echo "Working on it...\n";
        $wordsProvider = new WordsProvider();
        $words = $wordsProvider->getData();

        $result = [];
        $this->timer->start();
        foreach ($words as $word) {
            $result[] = $this->hyphenator->hyphenate($word);
        }
        $this->timer->stop();

        echo 'The process took ' . $this->timer->getDifference() . " seconds\nWritting to file...";

        $rezultFile = new SplFileObject('result.txt', 'w');
        if ($rezultFile->fwrite(implode("\n", $result) === 0)) {
            echo 'Somethign went wrong...';
            return;
        }
        echo ' Done!';
    }
}
