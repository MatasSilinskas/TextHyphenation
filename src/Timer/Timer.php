<?php

namespace TextHyphenation\Timer;

use Exception;

class Timer
{
    private $start;
    private $stop;

    public function start(): void
    {
        $this->start = microtime(true);
    }

    public function stop(): void
    {
        $this->stop = microtime(true);
    }

    public function reset(): void
    {
        $this->stop = null;
        $this->start = null;
    }

    /**
     * @return float
     * @throws Exception
     */
    public function getDifference(): float
    {
        if ($this->start === null) {
            throw new Exception('Timer hasn`t been started!');
        }

        if ($this->stop === null) {
            throw new Exception('Timer hasn`t been stopped!');
        }

        if (abs(($this->stop - $this->start)/$this->start) < 0.00000000000001) {
            return 0;
        }

        return $this->stop - $this->start;
    }
}
