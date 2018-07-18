<?php

namespace TextHyphenation\Executables;

use TextHyphenation\Database\TextHyphenationDatabase;
use TextHyphenation\DataConverters\DataConverter;

class Router implements ExecutableInterface
{
    private $requestVariables;
    private $database;
    private $converter;

    public function __construct(TextHyphenationDatabase $database, DataConverter $converter)
    {
        $this->requestVariables = new RequestVariables;
        $this->database = $database;
        $this->converter = $converter;
    }

    public function execute(): void
    {
        $className = ucfirst(explode('/', $this->requestVariables->getURI())[2]);
        $controller = 'TextHyphenation\\Controllers\\' . $className . 'Controller';
        $repository = 'TextHyphenation\\Database\\' . $className . 'Repository';
        $action = strtolower($this->requestVariables->getMethod()) . 'Action';
        $repository = new $repository($this->database);
        $controller = new $controller($repository, $this->converter);
        echo $controller->$action();
    }

    public function test(): void
    {
        $controller = 'TextHyphenation\\Controllers\\' . 'Words' . 'Controller';
        $repository = 'TextHyphenation\\Database\\' . 'Words' . 'Repository';
        $action = 'getAction';
        $repository = new $repository($this->database);
        $controller = new $controller($repository, $this->converter);
        echo $controller->$action();
    }
}
