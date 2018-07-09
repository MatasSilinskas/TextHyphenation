<?php

namespace TextHyphenation\Logger;

interface LoggerInterface
{
    /**
     * @param $message
     * @param array $context
     * @return mixed
     */
    public function emergency($message, array $context = array());

    /**
     * @param $message
     * @param array $context
     * @return mixed
     */
    public function alert($message, array $context = array());

    /**
     * @param $message
     * @param array $context
     * @return mixed
     */
    public function critical($message, array $context = array());

    /**
     * @param $message
     * @param array $context
     * @return mixed
     */
    public function error($message, array $context = array());

    /**
     * @param $message
     * @param array $context
     * @return mixed
     */
    public function warning($message, array $context = array());

    /**
     * @param $message
     * @param array $context
     * @return mixed
     */
    public function notice($message, array $context = array());

    /**
     * @param $message
     * @param array $context
     * @return mixed
     */
    public function info($message, array $context = array());

    /**
     * @param $message
     * @param array $context
     * @return mixed
     */
    public function debug($message, array $context = array());

    /**
     * @param $level
     * @param $message
     * @param array $context
     * @return mixed
     */
    public function log($level, $message, array $context = array());

}