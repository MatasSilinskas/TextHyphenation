<?php

namespace TextHyphenation\Controllers;

use TextHyphenation\DataConverters\DataConverter;

abstract class RESTController
{
    private $dataConverter;

    public const NOT_FOUND = 404;
    public const CONFLICT = 409;
    public const INTERNAL_SERVER_ERROR = 500;

    /**
     * RESTController constructor.
     * @param DataConverter $dataConverter
     */
    public function __construct(DataConverter $dataConverter)
    {
        $this->dataConverter = $dataConverter;
    }

    /**
     * @param array $keys
     * @param callable $function
     * @return array
     */
    protected function get(array $keys, callable $function): array
    {
        $userParams = [];
        foreach ($keys as $key) {
            $userParams[$key] = $_GET['id'];
        }

        $result = $function($userParams);

        if (empty($result)) {
            http_response_code(self::NOT_FOUND);
        }

        return $result;
    }

    /**
     * @param array $keys
     * @param $function
     * @return int|null
     */
    protected function put(array $keys, callable $function): ?int
    {
        return $this->execute($keys, $function);
    }

    /**
     * @param array $keys
     * @param callable $function
     * @return int|null
     */
    protected function post(array $keys, callable $function): ?int
    {
        $result = $function($this->getUserParams($keys));
        return $this->validateResult($result, self::CONFLICT);
    }

    /**
     * @param array $keys
     * @param callable $function
     * @return int|null
     */
    protected function delete(array $keys, callable $function): ?int
    {
        return $this->execute($keys, $function);
    }

    private function execute(array $keys, callable $function): ?int
    {
        $result = $function($this->getUserParams($keys));
        return $this->validateResult($result);
    }

    /**
     * @param int|null $result
     * @param int $codeOnZeroResults
     * @return int|null
     */
    private function validateResult(?int $result, int $codeOnZeroResults = self::NOT_FOUND): ?int
    {
        if ($result === null) {
            http_response_code(self::INTERNAL_SERVER_ERROR);
        } elseif ($result === 0) {
            http_response_code($codeOnZeroResults);
        }

        return $result;
    }

    /**
     * @param array $keys
     * @return array
     */
    private function getUserParams(array $keys): array
    {
        $encoded = $this->dataConverter->decode(file_get_contents("php://input"));
        
        $userParams = [];
        foreach ($keys as $key) {
            $userParams[$key] = $encoded[$key];
        }

        return $userParams;
    }
}
