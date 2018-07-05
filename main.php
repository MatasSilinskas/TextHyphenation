<?php

require_once 'Psr4Autoloader.php';

use Loader\Psr4AutoloaderClass;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use TextHyphenation\Console\Console;
use TextHyphenation\DataProviders\PatternsProvider;
use TextHyphenation\Hyphenators\Hyphenator;
use TextHyphenation\Timer\Timer;

$loader = new Psr4AutoloaderClass();
$loader->register();
$loader->addRequiredNamespaces();

$logger = new Logger('Hyphenator');
$logger->pushHandler(new StreamHandler('hyphenator.log'));
$patternsProvider = new PatternsProvider();
$hyphenator = new Hyphenator($patternsProvider->getData(), $logger);
$timer = new Timer();

$console = new Console($hyphenator, $timer);
$console->execute();
