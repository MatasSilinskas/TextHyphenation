<?php

require_once 'Autoloader.php';

use Loader\Autoloader;
use TextHyphenation\Cache\ArrayCachePool;
use TextHyphenation\Console\Console;
use TextHyphenation\DataProviders\PatternsProvider;
use TextHyphenation\Hyphenators\CachingHyphenator;
use TextHyphenation\Hyphenators\Hyphenator;
use TextHyphenation\Hyphenators\RegexHyphenator;
use TextHyphenation\Logger\FileLogger;
use TextHyphenation\Timer\Timer;

$config = include 'config.php';

$loader = new Autoloader;
$loader->addNameSpaces($config['namespaces']);
$logger = new FileLogger($config['hyphenator']);

$cache = new ArrayCachePool;
$patternsProvider = new PatternsProvider;

$hyphenator = new RegexHyphenator($patternsProvider->getData(), $logger);
$timer = new Timer;

$console = new Console($hyphenator, $timer);
$console->execute();
