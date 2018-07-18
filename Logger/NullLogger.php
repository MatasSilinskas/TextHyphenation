<?php

namespace TextHyphenation\Logger;

class NullLogger implements LoggerInterface
{

    /**
     * @param string $message
     * @param array $context
     */
    public function emergency(string $message, array $context = []): void
    {
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function alert(string $message, array $context = []): void
    {
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function critical(string $message, array $context = []): void
    {
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error(string $message, array $context = []): void
    {
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning(string $message, array $context = []): void
    {
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function notice(string $message, array $context = []): void
    {
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function info(string $message, array $context = []): void
    {
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug(string $message, array $context = []): void
    {
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, string $message, array $context = []): void
    {
    }
}
