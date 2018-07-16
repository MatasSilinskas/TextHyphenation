<?php

namespace TextHyphenation\RestAPI;

use TextHyphenation\Database\DatabaseProvider;

class REST
{
    private $database;

    /**
     * REST constructor.
     * @param $database
     */
    public function __construct(DatabaseProvider $database)
    {
        $this->database = $database;
    }

    /**
     * @param array $keys
     * @param $function
     * @return int|null
     */
    protected function put(array $keys, $function): ?int
    {
        $userParams = $this->getUserParams($keys);
        $result = $function($userParams);
        return $this->validateResult($result);
    }

    protected function post(array $params, $function)
    {
        $userParams = [];
        foreach ($params as $param) {
            $userParams[$param] = $_POST[$param];
        }

        $function($userParams);
        //check for mistakes
    }

    /**
     * @param array $keys
     * @param callable $function
     * @return int|null
     */
    protected function delete(array $keys, callable $function): ?int
    {
        $userParams = $this->getUserParams($keys);
        $result = $function($userParams);
        return $this->validateResult($result);
    }

    /**
     * @param string $param
     * @return string
     */
    private function getInputParam(string $param): string
    {
        parse_str(file_get_contents("php://input"),$post_vars);
        $vars = array_shift($post_vars) . '';
        preg_match('#"' . $param . '"(?:\W+)(\w+)#i', $vars, $match);
        return $match[1];
    }

    /**
     * @param int|null $result
     * @return int|null
     */
    private function validateResult(?int $result): ?int
    {
        if ($result === null) {
            http_response_code(500);
        } elseif ($result === 0) {
            http_response_code(404);
        }

        return $result;
    }

    /**
     * @param array $keys
     * @return array
     */
    private function getUserParams(array $keys): array
    {
        $userParams = [];
        foreach ($keys as $key) {
            $userParams[$key] = $this->getInputParam($key);
        }

        return $userParams;
    }
}