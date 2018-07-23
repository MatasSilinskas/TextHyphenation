<?php

return [
    'hyphenatorLogFile' => 'hyphenator.log',
    'namespaces' => [
        'TextHyphenation\\' => '/src/',
    ],
    'databaseConfig' => [
        'dsn' => 'mysql:host=localhost;dbname=hyphenation',
        'username' => 'root',
        'password' => 'password',
        'dbname' => 'hyphenation',
    ],
    'hyphenatorType' => 'normal'
];
