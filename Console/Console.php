<?php

namespace TextHyphenation\Console;

use SplFileObject;
use TextHyphenation\DataProviders\WordsProvider;
use TextHyphenation\Logger\LoggerInterface;

class Console
{
    private $tools;
    private $logger;

    /**
     * Console constructor.
     * @param ToolsInterface $tools
     */
    public function __construct(ToolsInterface $tools, LoggerInterface $logger)
    {
        $this->tools = $tools;
        $this->logger = $logger;
    }

    public function execute() : void
    {
        echo 'Would you like to hyphenate words from file or enter them yourself?[FILE/yourself]';
        $option = $this->getConsoleInput();
        if ($option === 'yourself') {
            $this->takeInputFromUser();
        } elseif ($option === 'file' || $option === '') {
            $this->takeInputFromFile();
        }
    }

    /**
     * @return string
     */
    private function getConsoleInput() : string
    {
        return substr(fgets(STDIN), 0, -1);
    }

    private function takeInputFromUser() : void
    {
        while (true) {
            echo 'Enter a word you want to hyphenate (or enter :exit to leave): ';
            $input = $this->getConsoleInput();
            if ($input === ':exit') {
                break;
            }

            $result = $this->tools->modify($input);

            if ($input === $result['result']) {
                $this->logger->warning("Hyphenated form of the word $input is equal to it`s original form.");
            }

            echo $result['result'] . "\n";
            if (isset($result['time'])) {
                echo 'The process took ' . $result['time'] . " seconds\n";
            }
        }
    }

    private function takeInputFromFile() : void
    {
        echo "Working on it...\n";
        $wordsProvider = new WordsProvider();
        $words = $wordsProvider->getData();

        $result = $this->tools->modifyMany($words);

        if (isset($result['time'])) {
            echo 'The process took ' . $result['time'] . " seconds\n";
        }

        echo "Writing to file...";

        $rezultFile = new SplFileObject('result.txt', 'w');
        if ($rezultFile->fwrite(implode("\n", $result['result']) === 0)) {
            echo 'Something went wrong...';
            return;
        }
        echo ' Done!';
    }
}
