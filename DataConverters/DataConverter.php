<?php

namespace TextHyphenation\DataConverters;

abstract class DataConverter
{
    /**
     * @var DataConverter|null
     */
    private $successor = null;

    public function __construct(DataConverter $converter = null)
    {
        $this->successor = $converter;
    }

    /**
     * @param string $data
     * @return array
     */
    final public function decode(string $data): array
    {
        $processed = $this->processDecoding($data);

        if ($processed === null) {
            if ($this->successor !== null) {
                $processed = $this->successor->decode($data);
            }
        }

        return $processed;
    }

    /**
     * @param string $data
     * @return array|null
     */
    abstract protected function processDecoding(string $data): ?array;

}