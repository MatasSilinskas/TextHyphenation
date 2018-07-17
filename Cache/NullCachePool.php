<?php

namespace TextHyphenation\Cache;


class NullCachePool implements CacheInterface
{

    /**
     * @param string $key
     * @param null $default
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function get(string $key, $default = null)
    {
        return $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param null|int|\DateInterval $ttl
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function set(string $key, $value, $ttl = null): bool
    {
        return true;
    }

    /**
     * @param string $key
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function delete(string $key): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
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
        return [];
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
        return true;
    }

    /**
     * @param string $key
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function has(string $key): bool
    {
        return false;
    }
}