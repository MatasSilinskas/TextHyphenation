<?php

require_once 'Autoloader.php';

use Loader\Autoloader;
use TextHyphenation\Cache\ArrayCachePool;
use TextHyphenation\Executables\API;
use TextHyphenation\Executables\Console;
use TextHyphenation\Executables\Tools;
use TextHyphenation\Database\DatabaseProvider;
use TextHyphenation\DataProviders\PatternsProvider;
use TextHyphenation\Hyphenators\CachingHyphenator;
use TextHyphenation\Hyphenators\Hyphenator;
use TextHyphenation\Hyphenators\ProxyHyphenator;
use TextHyphenation\Hyphenators\RegexHyphenator;
use TextHyphenation\Logger\FileLogger;
use TextHyphenation\RestAPI\TextHyphenationAPI;
use TextHyphenation\Timer\Timer;

$config = include 'config.php';

$loader = new Autoloader;
$loader->addNameSpaces($config['namespaces']);

$database = new DatabaseProvider(
    $config['databaseConfig']['dsn'],
    $config['databaseConfig']['username'],
    $config['databaseConfig']['password']
);

if (isset($_SERVER['REQUEST_METHOD'])) {
    $rest = new TextHyphenationAPI($database);
    $executable = new API($rest);
} else {
    $logger = new FileLogger($config['hyphenatorLogFile']);
    $cache = new ArrayCachePool;
    $patternsProvider = new PatternsProvider;

    $hyphenator = new ProxyHyphenator($patternsProvider->getData());
    $timer = new Timer;

    $tools = new Tools($hyphenator, $timer, $database);

    $executable = new Console($tools, $logger);
}

$executable->execute();
