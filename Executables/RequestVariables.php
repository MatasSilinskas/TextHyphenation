<?php

namespace TextHyphenation\Executables;

class RequestVariables
{
    private $server;

    public function __construct()
    {
        $this->server = $_SERVER;
    }

    /**
     * @return string
     */
    public function getURI(): string
    {
        return $this->server['REQUEST_URI'];
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->server['REQUEST_METHOD'];
    }
}
