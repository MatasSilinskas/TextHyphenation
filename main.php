<?php

require_once 'Autoloader.php';

use Loader\Autoloader;
use TextHyphenation\Cache\ArrayCachePool;
use TextHyphenation\Console\Console;
use TextHyphenation\Console\Tools;
use TextHyphenation\DataProviders\DatabaseProvider;
use TextHyphenation\DataProviders\PatternsProvider;
use TextHyphenation\Hyphenators\CachingHyphenator;
use TextHyphenation\Hyphenators\Hyphenator;
use TextHyphenation\Hyphenators\RegexHyphenator;
use TextHyphenation\Logger\FileLogger;
use TextHyphenation\Timer\Timer;

$config = include 'config.php';

$loader = new Autoloader;
$loader->addNameSpaces($config['namespaces']);



//$database = new DatabaseProvider();
//$database->importPatterns(['vienas', 'du']);


$logger = new FileLogger($config['hyphenator']);
$cache = new ArrayCachePool;
$patternsProvider = new PatternsProvider;

$hyphenator = new Hyphenator($patternsProvider->getData());
$timer = new Timer;
$database = new DatabaseProvider();

$tools = new Tools($hyphenator, $timer, $database);
$console = new Console($tools, $logger);
$console->execute();
