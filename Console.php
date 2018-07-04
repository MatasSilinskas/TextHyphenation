<?php

use DataProviders\PatternsProvider;
use DataProviders\WordsProvider;
use Hyphenators\NewHyphenator;
use Timer\Timer;

spl_autoload_register(function ($className) {
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    require_once __DIR__ . DIRECTORY_SEPARATOR . $className . '.php';
});

$patternsProvider = new PatternsProvider();
$hyphenator = new NewHyphenator($patternsProvider);

$timer = new Timer();

echo 'Would you like to hyphenate words from file or enter them yourself?[FILE/yourself]';
$option = getConsoleInput();

if ($option === 'yourself') {
    while (true) {
        echo 'Enter a word you want to hyphenate (or enter :exit to leave): ';
        $input = getConsoleInput();
        if ($input === ':exit') {
            break;
        }

        $timer->start();
        $word = $hyphenator->hyphenate($input);
        $timer->stop();

        echo $word . "\n";
        echo 'The process took ' . $timer->getDifference() . " seconds\n";
        $timer->reset();
    }
} else {
    echo "Working on it...\n";
    $wordsProvider = new WordsProvider();
    $words = $wordsProvider->getData();

    $i = 0;
    $result = [];
    $timer->start();
    foreach ($words as $word) {
        $result[] = $hyphenator->hyphenate($word);
    }
    $timer->stop();

    echo 'The process took ' . $timer->getDifference() . " seconds\nWritting to file...";

    $rezultFile = new SplFileObject('result.txt', 'w');
    $written = $rezultFile->fwrite(implode("\n", $result));
    echo ' Done!';
}

function getConsoleInput() {
    return substr(fgets(STDIN), 0, -1);
}
