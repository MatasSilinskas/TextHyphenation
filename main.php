<?php

require_once 'Autoloader.php';

use Loader\Autoloader;
use TextHyphenation\Cache\ArrayCachePool;
use TextHyphenation\Cache\CacheInterface;
use TextHyphenation\Container\Container;
use TextHyphenation\Database\PatternsRepository;
use TextHyphenation\Database\TextHyphenationDatabase;
use TextHyphenation\Database\WordsRepository;
use TextHyphenation\DataConverters\DataConverter;
use TextHyphenation\DataConverters\JSONConverter;
use TextHyphenation\DataConverters\XMLConverter;
use TextHyphenation\DataProviders\ProviderFactory;
use TextHyphenation\Executables\Router;
use TextHyphenation\Executables\Console;
use TextHyphenation\Executables\Facade;
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
$container->set(TextHyphenationDatabase::class, function () use ($config) {
    return new TextHyphenationDatabase(
        $config['databaseConfig']['dsn'],
        $config['databaseConfig']['username'],
        $config['databaseConfig']['password'],
        $config['databaseConfig']['dbname']
    );
});
$container->set(LoggerInterface::class, function () use ($config) {
    return new FileLogger($config['hyphenatorLogFile']);
});

$container->set(CacheInterface::class, function () use ($config) {
    return new ArrayCachePool();
});

$container->set(HyphenatorFactory::class, function (Container $container) {
    /* @var ProviderFactory $providerFactory */
    $providerFactory = $container->get(ProviderFactory::class);
    $patternsProvider = $providerFactory->createProvider('patterns');
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
    $wordsRepository = $container->get(WordsRepository::class);
    $patternsRepository = $container->get(PatternsRepository::class);
    $timer = $container->get(Timer::class);
    return new Facade($hyphenator, $timer, $wordsRepository, $patternsRepository);
});

if (isset($_SERVER['REQUEST_METHOD'])) {
    $executable = $container->get(Router::class);
} else {
    $executable = $container->get(Console::class);
}

$executable->execute();
