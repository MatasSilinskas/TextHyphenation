<?php

namespace TextHyphenation\Timer;

use Exception;

class Timer
{
    private $start;
    private $timePost;

    public function start()
    {
        $this->start = microtime(true);
    }

    public function stop()
    {
        $this->timePost = microtime(true);
    }

    public function reset()
    {
        $this->timePost = null;
        $this->start = null;
    }

    /**
     * @return float
     * @throws Exception
     */
    public function getDifference() : float
    {
        if ($this->start === null) {
            throw new Exception('Timer hasn`t been started!');
        }

        if ($this->timePost === null) {
            throw new Exception('Timer hasn`t been stopped!');
        }
        return $this->timePost - $this->start;
    }
}
