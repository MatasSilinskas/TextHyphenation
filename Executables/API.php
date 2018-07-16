<?php

namespace TextHyphenation\Executables;


use TextHyphenation\RestAPI\TextHyphenationAPI;

class API implements ExecutableInterface
{
    private $rest;

    /**
     * API constructor.
     * @param TextHyphenationAPI $rest
     */
    public function __construct(TextHyphenationAPI $rest)
    {
        $this->rest = $rest;
    }

    public function execute()
    {
        $table = end(explode('/', $_SERVER['REQUEST_URI']));
        switch ($table) {
            case 'patterns':
                $this->executeForPatternsTable();
                break;
            case 'words':
                $this->executeForWordsTable();
                break;
        }
    }

    public function executeForWordsTable()
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                var_dump('aa');
                echo json_encode($this->rest->getWords());
                break;
            case 'DELETE':
                $this->rest->deleteWord();
                break;
        }
    }

    public function executeForPatternsTable()
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $this->rest->addPattern();
                break;
            case 'PUT':
                $this->rest->updatePattern();
                break;
        }
    }
}