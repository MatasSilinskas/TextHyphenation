<?php

namespace TextHyphenation\Container;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

class Container implements ContainerInterface
{

    private $definitions = [];

    /**
     * @param string $id
     * @param Closure $value
     */
    public function set(string $id, Closure $value)
    {
        $this->definitions[$id] = $value;
    }

    /**
     * @param string $id
     * @return mixed
     * @throws ClassNotFoundException
     */
    public function get(string $id)
    {
        if ($this->has($id)) {
            return $this->definitions[$id]($this);
        }

        try {
            $reflector = new ReflectionClass($id);
        } catch (ReflectionException $exception) {
            throw new ClassNotFoundException();
        }

        if (!$reflector->isInstantiable()) {
            throw new ClassNotFoundException();
        }

        /** @var \ReflectionMethod|null */
        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $id();
        }

        $dependencies = array_map(
            function (ReflectionParameter $dependency) use ($id) {
                if ($dependency->getClass() === null) {
                    throw new ClassNotFoundException();
                }

                return $this->get($dependency->getClass()->getName());
            }, $constructor->getParameters()
        );

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->definitions[$id]);
    }
}