<?php

namespace TextHyphenation\Hyphenators;

use TextHyphenation\Logger\LoggerInterface;

class Log extends HyphenatorDecorator
{
    private $logger;

    public function __construct(HyphenatorInterface $hyphenator, LoggerInterface $logger)
    {
        parent::__construct($hyphenator);
        $this->logger = $logger;
    }

    /**
     * @param string $word
     * @param array $usedPatterns
     * @return string
     */
    public function hyphenate(string $word, array &$usedPatterns = []): string
    {
        $hyphenated = $this->getHyphenator()->hyphenate($word, $usedPatterns);

        if ($hyphenated === $word) {
            $this->logger->warning("Hyphenated form of the word $word is equal to it`s original form.");
        }

        return $hyphenated;
    }
}
