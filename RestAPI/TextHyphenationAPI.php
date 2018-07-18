<?php

namespace TextHyphenation\RestAPI;

use TextHyphenation\Database\DatabaseProvider;
use TextHyphenation\DataConverters\DataConverter;

class TextHyphenationAPI extends REST
{
    private $database;

    /**
     * TextHyphenationAPI constructor.
     * @param DatabaseProvider $database
     * @param DataConverter $dataConverter
     */
    public function __construct(DatabaseProvider $database, DataConverter $dataConverter)
    {
        parent::__construct($dataConverter);
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

    /**
     * @return int|null
     */
    public function addPattern(): ?int
    {
        $params = ['pattern'];
        return $this->post($params, function (array $params) {
            return $this->database->insertPattern($params['pattern']);
        });
    }

    /**
     * @return array
     */
    public function getWords(): array
    {
        return $this->database->getWords();
    }

    public function getWord(): array
    {
        $params = ['id'];
        return $this->get($params, function (array $params) {
            return $this->database->getWord($params['id']);
        });
    }

    /**
     * @return int|null
     */
    public function deleteWord(): ?int
    {
        $params = ['word'];
        return $this->delete($params, function (array $params) {
            return $this->database->deleteWord($params['word']);
        });
    }

    public function getPatterns(): array
    {
        return $this->database->getPatterns();
    }

    public function getPattern(): array
    {
        $params = ['id'];
        return $this->get($params, function (array $params) {
            return $this->database->getPattern($params['id']);
        });
    }
}
