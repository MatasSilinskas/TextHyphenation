<?php

return [
    'hyphenatorLogFile' => 'hyphenator.log',
    'namespaces' => [
        'TextHyphenation\Executables' => '/Executables/',
        'TextHyphenation\DataProviders' => '/DataProviders/',
        'TextHyphenation\Hyphenators' => '/Hyphenators/',
        'TextHyphenation\Timer' => '/Timer/',
        'TextHyphenation\Cache' => '/Cache/',
        'TextHyphenation\Email' => '/Email/',
        'TextHyphenation\Logger' => '/Logger/',
        'TextHyphenation\Database' => '/Database/',
        'TextHyphenation\RestAPI' => '/RestAPI/',
        'TextHyphenation\Container' => '/Container/',
    ],
    'databaseConfig' => [
        'dsn' => 'mysql:host=localhost',
        'username' => 'root',
        'password' => 'password',
    ],
    'hyphenatorType' => 'normal'
];
