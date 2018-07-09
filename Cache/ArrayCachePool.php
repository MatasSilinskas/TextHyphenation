<?php

namespace TextHyphenation\Cache;

class ArrayCachePool implements CacheInterface
{
    private $pool = [];

    /**
     * @param $key
     * @param null $default
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function get($key, $default = null)
    {
        $this->validateKey($key);
        if ($this->pool[$key] === null) {
            return $default;
        }

        return $this->pool[$key];
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param null $ttl
     * @return bool
     * @throws InvalidArgumentException
     */
    public function set(string $key, $value, $ttl = null): bool
    {
        $this->validateKey($key);
        $this->pool[$key] = $value;
        return true;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        unset($this->pool[$key]);
        return true;
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        $this->pool = [];
        return true;
    }

    /**
     * @param iterable $keys
     * @param mixed $default
     * @return iterable
     *
     * @throws InvalidArgumentException
     */
    public function getMultiple(iterable $keys, $default = null): iterable
    {
        $this->validateKeys($keys);
        return array_map(function ($key, $default) {
            return $this->get($key, $default);
        }, $keys, $default);
    }

    /**
     * @param iterable $values
     * @param null|int|\DateInterval $ttl
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function setMultiple(iterable $values, $ttl = null): bool
    {
        $this->validateKeys($values);
        foreach ($values as $key => $value) {
            $this->pool[$key] = $value;
        }

        return true;
    }

    /**
     * @param iterable $keys
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $this->validateKeys($keys);
        foreach ($keys as $key) {
            $this->delete($key);
        }
    }

    /**
     * @param string $key
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function has(string $key): bool
    {
        $this->validateKey($key);
        return isset($this->pool[$key]);
    }

    /**
     * @param $key
     * @throws InvalidArgumentException
     */
    private function validateKey($key)
    {
        if ($key === null) {
            throw new InvalidArgumentException('The key value is incorrect');
        }
    }

    /**
     * @param $keys
     * @throws InvalidArgumentException
     */
    private function validateKeys($keys)
    {
        if (!is_iterable($keys) && !is_array($keys)) {
            throw new InvalidArgumentException('Passed keys are not iterable!');
        }

        foreach ($keys as $key) {
            $this->validateKey($key);
        }
    }
}
