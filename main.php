<?php

require_once 'Autoloader.php';

use Loader\Autoloader;
use TextHyphenation\Cache\ArrayCachePool;
use TextHyphenation\Console\Console;
use TextHyphenation\DataProviders\PatternsProvider;
use TextHyphenation\Hyphenators\CachingHyphenator;
use TextHyphenation\Hyphenators\Hyphenator;
use TextHyphenation\Logger\FileLogger;
use TextHyphenation\Timer\Timer;

$loader = new Autoloader();
$loader->addRequiredNamespaces();

$logger = new FileLogger('hyphenator.log');
$cache = new ArrayCachePool();

$patternsProvider = new PatternsProvider();

$hyphenator = new CachingHyphenator($patternsProvider->getData(), $logger, $cache);
$timer = new Timer();

$console = new Console($hyphenator, $timer);
$console->execute();
