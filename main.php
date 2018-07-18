<?php

require_once 'Autoloader.php';

use Loader\Autoloader;
use TextHyphenation\Cache\ArrayCachePool;
use TextHyphenation\Cache\CacheInterface;
use TextHyphenation\Container\Container;
use TextHyphenation\DataConverters\DataConverter;
use TextHyphenation\DataConverters\JSONConverter;
use TextHyphenation\DataConverters\XMLConverter;
use TextHyphenation\Executables\API;
use TextHyphenation\Executables\Console;
use TextHyphenation\Executables\Facade;
use TextHyphenation\Database\DatabaseProvider;
use TextHyphenation\DataProviders\PatternsProvider;
use TextHyphenation\Executables\FacadeInterface;
use TextHyphenation\Hyphenators\Cache;
use TextHyphenation\Hyphenators\HyphenatorFactory;
use TextHyphenation\Hyphenators\HyphenatorInterface;
use TextHyphenation\Hyphenators\Log;
use TextHyphenation\Logger\FileLogger;
use TextHyphenation\Logger\LoggerInterface;
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
$container->set(LoggerInterface::class, function () use ($config) {
    return new FileLogger($config['hyphenatorLogFile']);
});

$container->set(CacheInterface::class, function () use ($config) {
    return new ArrayCachePool();
});

$container->set(HyphenatorFactory::class, function (Container $container) {
    $patternsProvider = $container->get(PatternsProvider::class);
    return new HyphenatorFactory($patternsProvider->getData());
});

$container->set(DataConverter::class, function () {
    return new JSONConverter(new XMLConverter());
});

$container->set(HyphenatorInterface::class, function (Container $container) use ($config) {
    $cache = $container->get(CacheInterface::class);
    $logger = $container->get(LoggerInterface::class);
    $factory = $container->get(HyphenatorFactory::class);
    return new Log(
        new Cache($factory->createHyphenator($config['hyphenatorType']), $cache),
        $logger
    );
});

$container->set(FacadeInterface::class, function (Container $container) {
    $hyphenator = $container->get(HyphenatorInterface::class);
    $database = $container->get(DatabaseProvider::class);
    $timer = $container->get(Timer::class);
    return new Facade($hyphenator, $timer, $database);
});

if (isset($_SERVER['REQUEST_METHOD'])) {
    $executable = $container->get(API::class);
} else {
    $executable = $container->get(Console::class);
}

$executable->execute();
