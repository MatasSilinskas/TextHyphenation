<?php

namespace TextHyphenation\RestAPI;


use TextHyphenation\Database\DatabaseProvider;

class TextHyphenationAPI extends REST
{
    private $database;

    /**
     * TextHyphenationAPI constructor.
     * @param DatabaseProvider $database
     */
    public function __construct(DatabaseProvider $database)
    {
        parent::__construct($database);
        $this->database = $database;
    }

    /**
     * @return int|null
     */
    public function updatePattern(): ?int
    {
        $params = ['oldPattern', 'newPattern'];
        return $this->put($params, function (array $params) {
            return $this->database->updatePattern($params);
        });
    }

    public function addPattern()
    {
        $params = ['pattern'];
        $this->post($params, function (array $params) {
            $this->database->insertPattern($params['pattern']);
        });
    }

    public function getWords(): array
    {
        return $this->database->getWords();
    }

    public function deleteWord(): ?int
    {
        $params = ['word'];
        return $this->delete($params, function (array $params) {
            return $this->database->deleteWord($params['word']);
        });
    }
}