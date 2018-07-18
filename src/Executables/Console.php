<?php

namespace TextHyphenation\Executables;

use SplFileObject;
use TextHyphenation\DataProviders\WordsProvider;

class Console implements ExecutableInterface
{
    private $facade;
    private const POSITIVE_ANSWER = 'yes';

    /**
     * Console constructor.
     * @param FacadeInterface $tools
     */
    public function __construct(FacadeInterface $tools)
    {
        $this->facade = $tools;
    }

    public function execute(): void
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
                $this->facade->setDatabaseUsage(true);
                echo 'Would you like to import patterns from file?[yes/NO]';
                $answer = strtolower($this->getConsoleInput());
                if ($answer === self::POSITIVE_ANSWER) {
                    $this->facade->importPatterns();
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
    private function getConsoleInput(): string
    {
        return substr(fgets(STDIN), 0, -1);
    }

    private function takeInputFromUser(): void
    {
        while (true) {
            echo 'Enter a word you want to hyphenate (or enter :exit to leave): ';
            $input = $this->getConsoleInput();
            if ($input === ':exit') {
                break;
            }

            $result = $this->facade->hyphenate($input);

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

    private function takeInputFromFile(): void
    {
        echo "Working on it...\n";
        $wordsProvider = new WordsProvider();
        $words = $wordsProvider->getData();

        $result = $this->facade->hyphenateMany($words);

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
