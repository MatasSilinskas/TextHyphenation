<?php

namespace TextHyphenation\Executables;

use TextHyphenation\Database\TextHyphenationDatabase;
use TextHyphenation\DataConverters\DataConverter;

class Router implements ExecutableInterface
{
    private $database;
    private $converter;

    public function __construct(TextHyphenationDatabase $database, DataConverter $converter)
    {
        $this->database = $database;
        $this->converter = $converter;
    }

    public function execute(): void
    {
        $uri = explode('/', $_SERVER['REQUEST_URI']);
        $method = $_SERVER['REQUEST_METHOD'];

        if ($uri[2] === 'api') {
            $className = ucfirst(explode('?', $uri[3])[0]);
            $controller = 'TextHyphenation\\Controllers\\' . $className . 'Controller';
            $repository = 'TextHyphenation\\Database\\' . $className . 'Repository';
            $action = strtolower($method) . 'Action';

            $repository = new $repository($this->database);
            $controller = new $controller($repository, $this->converter);
            echo $controller->$action();
        }
    }
}
