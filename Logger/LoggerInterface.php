<?php

namespace TextHyphenation\Logger;

interface LoggerInterface
{
    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function emergency(string $message, array $context = array());

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function alert(string $message, array $context = array());

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function critical(string $message, array $context = array());

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error(string $message, array $context = array());

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning(string $message, array $context = array());

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function notice(string $message, array $context = array());

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info(string $message, array $context = array());

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function debug(string $message, array $context = array());

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, string $message, array $context = array());
}
