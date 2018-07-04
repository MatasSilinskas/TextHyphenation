<?php

use Console\Console;
use DataProviders\PatternsProvider;
use Hyphenators\NewHyphenator;
use Timer\Timer;

spl_autoload_register(function ($className) {
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    require_once __DIR__ . DIRECTORY_SEPARATOR . $className . '.php';
});

$patternsProvider = new PatternsProvider();
$hyphenator = new NewHyphenator($patternsProvider);
$timer = new Timer();

$console = new Console($hyphenator, $timer);
$console->execute();
