<?php

namespace TextHyphenation\Controllers;

use TextHyphenation\Database\WordsRepository;
use TextHyphenation\DataConverters\DataConverter;

class WordsController extends RESTController
{
    private $repository;

    /**
     * WordsController constructor.
     * @param WordsRepository $repository
     * @param DataConverter $converter
     */
    public function __construct(WordsRepository $repository, DataConverter $converter)
    {
        parent::__construct($converter);
        $this->repository = $repository;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        $params = ['id'];
        if (isset($_GET[$params[0]])) {
            return json_encode($this->get($params, function (array $params) {
                return $this->repository->getWord($params['id']);
            }));
        }
        return json_encode($this->repository->getWords());
    }

    public function deleteAction(): void
    {
        $params = ['word'];
        $this->delete($params, function (array $params) {
            return $this->repository->deleteWord($params['word']);
        });
    }
}
