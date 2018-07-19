<?php

namespace TextHyphenation\DataProviders;


interface DataProvider
{
    /**
     * @return array
     */
    public function getData(): array;
}