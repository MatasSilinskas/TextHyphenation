<?php

namespace TextHyphenation\Logger;


use SplFileObject;

class FileLogger implements LoggerInterface
{
    /**
     * @var SplFileObject $file
     */
    private $file;

    public function __construct(string $fileName)
    {
        $this->file = new SplFileObject($fileName, 'a');
    }
    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function emergency(string $message, array $context = array())
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function alert(string $message, array $context = array())
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function critical(string $message, array $context = array())
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error(string $message, array $context = array())
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning(string $message, array $context = array())
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function notice(string $message, array $context = array())
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info(string $message, array $context = array())
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function debug(string $message, array $context = array())
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, string $message, array $context = array())
    {
        $this->file->fwrite('[' . date('Y M d') . '] ' . strtoupper($level) . ": $message\n");
    }
}