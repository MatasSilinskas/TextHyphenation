<?php

namespace TextHyphenation\DataConverters;

class JSONConverter extends DataConverter
{

    /**
     * @param string $data
     * @return array|null
     */
    protected function processDecoding(string $data): ?array
    {
        return json_decode($data, true);
    }
}
