<?php

namespace TextHyphenation\DataConverters;

class XMLConverter extends DataConverter
{
    /**
     * @param string $data
     * @return array|null
     */
    protected function processDecoding(string $data): ?array
    {
        $xml = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_encode($xml);
        return json_decode($json,TRUE);
    }
}