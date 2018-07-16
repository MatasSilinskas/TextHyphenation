<?php

namespace TextHyphenation\Executables;

use TextHyphenation\RestAPI\TextHyphenationAPI;

class API implements ExecutableInterface
{
    private $rest;

    private const PATTERNS_TABLE = 'patterns';
    private const WORDS_TABLE = 'words';

    private $requestVariables;

    /**
     * API constructor.
     * @param TextHyphenationAPI $rest
     */
    public function __construct(TextHyphenationAPI $rest)
    {
        $this->rest = $rest;
        $this->requestVariables = new RequestVariables;

    }

    public function execute(): void
    {
        $table = explode('/', $this->requestVariables->getURI())[2];
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
        switch ($this->requestVariables->getMethod()) {
            case 'GET':
                if (isset($_GET['id'])) {
                    echo json_encode($this->rest->getWord());
                    break;
                }
                echo json_encode($this->rest->getWords());
                break;
            case 'DELETE':
                $this->rest->deleteWord();
                break;
        }
    }

    public function executeForPatternsTable(): void
    {
        switch ($this->requestVariables->getMethod()) {
            case 'POST':
                $this->rest->addPattern();
                break;
            case 'PUT':
                $this->rest->updatePattern();
                break;
        }
    }
}
