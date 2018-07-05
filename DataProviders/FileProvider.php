<?php

namespace TextHyphenation\DataProviders;

use SplFileObject;

abstract class FileProvider
{
    protected $file;

    public function __construct(string $fileName)
    {
        $this->file = new SplFileObject($fileName);
    }

    public function getData() : array
    {
        $data = [];

        while (!$this->file->eof()) {
            $data[] = str_replace("\r\n", '', $this->file->current());
            $this->file->next();
        }

        return $data;
    }
}
