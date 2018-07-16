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
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                echo json_encode($this->rest->getWords());
                break;
            case 'POST':
                $this->rest->addPattern();
                break;
            case 'PUT':
                $this->rest->updatePattern();
                break;
            case 'DELETE':
                $this->rest->deleteWord();
                break;
        }
    }
}