<?php

namespace TextHyphenation\Controllers;

use TextHyphenation\Database\PatternsRepository;
use TextHyphenation\DataConverters\DataConverter;

class PatternsController extends RESTController
{
    private $repository;

    /**
     * PatternsController constructor.
     * @param PatternsRepository $repository
     * @param DataConverter $converter
     */
    public function __construct(PatternsRepository $repository, DataConverter $converter)
    {
        parent::__construct($converter);
        $this->repository = $repository;
    }

    public function getAction(): string
    {
        $params = ['id'];
        if (isset($_GET[$params[0]])) {
            return json_encode($this->get($params, function (array $params) {
                return $this->repository->getPattern($params['id']);
            }));
        }

        return json_encode($this->repository->getPatterns());
    }

    public function postAction(): void
    {
        $params = ['pattern'];
        $this->post($params, function (array $params) {
            return $this->repository->insertPattern($params['pattern']);
        });
    }

    public function updateAction(): void
    {
        $params = ['oldPattern', 'newPattern'];
        $this->put($params, function (array $params) {
            return $this->repository->updatePattern($params);
        });
    }
}
