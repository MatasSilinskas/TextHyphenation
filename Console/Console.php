<?php

namespace TextHyphenation\Console;

use SplFileObject;
use TextHyphenation\DataProviders\WordsProvider;
use TextHyphenation\Logger\LoggerInterface;

class Console
{
    private $tools;
    private $logger;
    private const POSITIVE_ANSWER = 'yes';

    /**
     * Console constructor.
     * @param ToolsInterface $tools
     * @param LoggerInterface $logger
     */
    public function __construct(ToolsInterface $tools, LoggerInterface $logger)
    {
        $this->tools = $tools;
        $this->logger = $logger;
    }

    public function execute() : void
    {
        echo "Choose an option (DEFAULT - 3):\n";
        echo "1) Use file\n";
        echo "2) Use database\n";
        echo "3) Enter words yourself\n";
        $option = $this->getConsoleInput();
        switch ($option) {
            case 1:
                $this->takeInputFromFile();
                break;
            case 2:
                $this->tools->setUseDatabase(true);
                echo 'Would you like to import patterns from file?[yes/NO]';
                $answer = strtolower($this->getConsoleInput());
                if ($answer === $this::POSITIVE_ANSWER) {
                    $this->tools->importPatterns();
                }
                $this->takeInputFromUser();
                break;
            default:
                $this->takeInputFromUser();
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
            if (isset($result['patterns']) && !empty($result['patterns'])) {
                foreach ($result['patterns'] as $word => $patterns) {
                    echo 'The word "' . $word . '" used these patterns: ' . implode(', ', $patterns) . "\n";
                }
            }
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

        $resultFile = new SplFileObject('result.txt', 'w');
        if ($resultFile->fwrite(implode("\n", $result['result']) === 0)) {
            echo 'Something went wrong...';
            return;
        }
        echo ' Done!';
    }
}
