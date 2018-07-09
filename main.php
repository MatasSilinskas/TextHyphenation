<?php

require_once 'Psr4Autoloader.php';

use Cache\Adapter\PHPArray\ArrayCachePool;
use Loader\Psr4AutoloaderClass;
use TextHyphenation\Console\Console;
use TextHyphenation\DataProviders\PatternsProvider;
use TextHyphenation\Hyphenators\CachingHyphenator;
use TextHyphenation\Hyphenators\Hyphenator;
use TextHyphenation\Logger\FileLogger;
use TextHyphenation\Timer\Timer;

$loader = new Psr4AutoloaderClass();
$loader->register();
$loader->addRequiredNamespaces();

$logger = new FileLogger('hyphenator.log');
$cache = new ArrayCachePool();

$patternsProvider = new PatternsProvider();

$hyphenator = new CachingHyphenator($patternsProvider->getData(), $logger, $cache);
$timer = new Timer();

$console = new Console($hyphenator, $timer);
$console->execute();
