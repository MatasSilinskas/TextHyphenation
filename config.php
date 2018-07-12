<?php

return [
    'hyphenatorLogFile' => 'hyphenator.log',
    'namespaces' => [
        'TextHyphenation\Console' => '/Console/',
        'TextHyphenation\DataProviders' => '/DataProviders/',
        'TextHyphenation\Hyphenators' => '/Hyphenators/',
        'TextHyphenation\Timer' => '/Timer/',
        'TextHyphenation\Cache' => '/Cache/',
        'TextHyphenation\Email' => '/Email/',
        'TextHyphenation\Logger' => '/Logger/',
        'TextHyphenation\Database' => '/Database/',
    ],
    'databaseConfig' => [
        'dsn' => 'mysql:host=localhost',
        'username' => 'root',
        'password' => 'password',
    ]
];
