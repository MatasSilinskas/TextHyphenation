<?php

namespace TextHyphenation\RestAPI;

abstract class REST
{

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
            http_response_code(404);
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
     * @param array $params
     * @param callable $function
     * @return int|null
     */
    protected function post(array $params, callable $function): ?int
    {
        $userParams = [];
        foreach ($params as $param) {
            $userParams[$param] = $_POST[$param];
        }

        $result = $function($userParams);
        return $this->validateResult($result, 409);
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
     * @param string $param
     * @return string
     */
    private function getInputParam(string $param): string
    {
        parse_str(file_get_contents("php://input"), $info);
        $vars = array_shift($info) . '';
        preg_match('#"' . $param . '"(?:\W+)(\w+)#i', $vars, $match);
        return $match[1];
    }

    /**
     * @param int|null $result
     * @param int $codeOnZeroResults
     * @return int|null
     */
    private function validateResult(?int $result, int $codeOnZeroResults = 404): ?int
    {
        if ($result === null) {
            http_response_code(500);
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
        $userParams = [];
        foreach ($keys as $key) {
            $userParams[$key] = $this->getInputParam($key);
        }

        return $userParams;
    }
}
