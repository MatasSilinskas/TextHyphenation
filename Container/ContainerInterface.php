<?php

namespace TextHyphenation\Container;

interface ContainerInterface
{
    /**
     * @param string $parameter
     * @return mixed
     */
    public function get(string $parameter);

    /**
     * @param string $parameter
     * @return bool
     */
    public function has(string $parameter): bool;
}
