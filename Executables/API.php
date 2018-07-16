<?php

namespace TextHyphenation\Executables;


use TextHyphenation\RestAPI\TextHyphenationAPI;

class API implements ExecutableInterface
{
    private $rest;

    private const PATTERNS_TABLE = 'patterns';
    private const WORDS_TABLE = 'words';

    /**
     * API constructor.
     * @param TextHyphenationAPI $rest
     */
    public function __construct(TextHyphenationAPI $rest)
    {
        $this->rest = $rest;
    }

    public function execute(): void
    {
        $table = end(explode('/', $_SERVER['REQUEST_URI']));
        switch ($table) {
            case self::PATTERNS_TABLE:
                $this->executeForPatternsTable();
                break;
            case self::WORDS_TABLE:
                $this->executeForWordsTable();
                break;
        }
    }

    public function executeForWordsTable(): void
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                echo json_encode($this->rest->getWords());
                break;
            case 'DELETE':
                $this->rest->deleteWord();
                break;
        }
    }

    public function executeForPatternsTable(): void
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