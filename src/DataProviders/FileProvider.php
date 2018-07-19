<?php

namespace TextHyphenation\DataProviders;

use SplFileObject;

class FileProvider implements DataProvider
{
    protected $file;

    /**
     * FileProvider constructor.
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        $this->file = new SplFileObject($fileName);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [];

        while (!$this->file->eof()) {
            $data[] = str_replace("\r\n", '', $this->file->current());
            $this->file->next();
        }

        return $data;
    }
}
