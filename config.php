<?php

return [
    'hyphenatorLogFile' => 'hyphenator.log',
    'namespaces' => [
        'TextHyphenation\\' => '/src/',
    ],
    'databaseConfig' => [
        'dsn' => 'mysql:host=localhost',
        'username' => 'root',
        'password' => 'password',
    ],
    'hyphenatorType' => 'normal'
];
