<?php

require_once 'Autoloader.php';

use Loader\Autoloader;
use TextHyphenation\Cache\ArrayCachePool;
use TextHyphenation\Container\Container;
use TextHyphenation\Executables\API;
use TextHyphenation\Executables\Console;
use TextHyphenation\Executables\Tools;
use TextHyphenation\Database\DatabaseProvider;
use TextHyphenation\DataProviders\PatternsProvider;
use TextHyphenation\Executables\ToolsInterface;
use TextHyphenation\Hyphenators\CachingHyphenator;
use TextHyphenation\Hyphenators\Hyphenator;
use TextHyphenation\Hyphenators\HyphenatorInterface;
use TextHyphenation\Hyphenators\ProxyHyphenator;
use TextHyphenation\Hyphenators\RegexHyphenator;
use TextHyphenation\Logger\FileLogger;
use TextHyphenation\Logger\LoggerInterface;
use TextHyphenation\RestAPI\TextHyphenationAPI;
use TextHyphenation\Timer\Timer;

$config = include 'config.php';

$loader = new Autoloader;
$loader->addNameSpaces($config['namespaces']);

$container = new Container();
$container->set(DatabaseProvider::class, function () use ($config) {
    return new DatabaseProvider(
        $config['databaseConfig']['dsn'],
        $config['databaseConfig']['username'],
        $config['databaseConfig']['password']);
});

if (isset($_SERVER['REQUEST_METHOD'])) {
    $executable = $container->get(API::class);
} else {
    $container->set(LoggerInterface::class, function () use ($config) {
        return new FileLogger($config['hyphenatorLogFile']);
    });

    $container->set(HyphenatorInterface::class, function (Container $container) {
        $patternsProvider = $container->get(PatternsProvider::class);
        return new ProxyHyphenator($patternsProvider->getData());
    });

    $container->set(ToolsInterface::class, function (Container $container) {
        $hyphenator = $container->get(HyphenatorInterface::class);
        $database = $container->get(DatabaseProvider::class);
        $timer = $container->get(Timer::class);
        return new Tools($hyphenator, $timer, $database);
    });

    $executable = $container->get(Console::class);
}

$executable->execute();
