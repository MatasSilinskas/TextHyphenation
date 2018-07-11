<?php

namespace TextHyphenation\Console;

use TextHyphenation\Hyphenators\HyphenatorInterface;
use TextHyphenation\Timer\Timer;

class Tools implements ToolsInterface
{
    private $hyphenator;
    private $timer;

    /**
     * Tools constructor.
     * @param $hyphenator
     * @param $timer
     */
    public function __construct(HyphenatorInterface $hyphenator, Timer $timer)
    {
        $this->hyphenator = $hyphenator;
        $this->timer = $timer;
    }

    /**
     * @param string $sentence
     * @return array
     * @throws \Exception
     */
    public function modify(string $sentence): array
    {
        $this->timer->reset();
        $this->timer->start();
        $result = $this->hyphenator->hyphenate($sentence);
        $this->timer->stop();

        return [
            'time' => $this->timer->getDifference(),
            'result' => $result
        ];
    }

    /**
     * @param array $sentences
     * @return array
     * @throws \Exception
     */
    public function modifyMany(array $sentences) : array
    {
        $this->timer->reset();
        $this->timer->start();
        $result = [];
        foreach ($sentences as $sentence) {
            $result[] = $this->hyphenator->hyphenate($sentence);
        }
        $this->timer->stop();

        return [
            'time' => $this->timer->getDifference(),
            'result' => $result
        ];
    }
}