<?php

namespace DataProviders;

class ProviderFactory
{
    /**
     * @param string $dataType
     * @return FileProvider
     * @throws \Exception
     */
    public static function create(string $dataType) : FileProvider
    {
        $dataType = strtolower($dataType);
        if ($dataType === 'patterns') {
            return new PatternsProvider();
        }
        elseif ($dataType === 'words') {
            return new WordsProvider();
        }

        throw new \Exception("$dataType data type doesn`t exist!");
    }
}