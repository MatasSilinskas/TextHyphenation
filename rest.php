<?php

require_once 'Autoloader.php';

use Loader\Autoloader;
use TextHyphenation\Database\DatabaseProvider;
use TextHyphenation\RestAPI\REST;
use TextHyphenation\RestAPI\TextHyphenationAPI;

$config = include 'config.php';

$loader = new Autoloader;
$loader->addNameSpaces($config['namespaces']);

$database = new DatabaseProvider(
    $config['databaseConfig']['dsn'],
    $config['databaseConfig']['username'],
    $config['databaseConfig']['password']
);

$rest = new TextHyphenationAPI($database);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($rest->getWords());
        break;
    case 'POST':
        $rest->addPattern();
        break;
    case 'PUT':
        $rest->updatePattern();
        break;
    case 'DELETE':
        $rest->deleteWord();
        break;
}