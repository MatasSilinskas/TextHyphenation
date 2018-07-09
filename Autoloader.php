<?php

namespace Loader;

class Autoloader
{
    private $directories;

    /**
     * Autoloader constructor.
     */
    public function __construct()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    private function loadClass(string $class)
    {
        $nameSpaces = explode("\\", $class);
        $className = array_pop($nameSpaces);
        $fullNameSpace = implode('\\', $nameSpaces);
        if (isset($this->directories[$fullNameSpace])) {
            foreach ($this->directories[$fullNameSpace] as $directory) {
                $file = $directory . DIRECTORY_SEPARATOR . $className . '.php';
                if (file_exists($file)) {
                    include $file;
                    return $file;
                }
            }
        }
        return false;
    }

    public function addNameSpace(string $prefix, string $directory)
    {
        $this->directories[$prefix][] = $directory;
    }

    public function addRequiredNamespaces()
    {
        $this->addNamespace(
            'TextHyphenation\Console',
            __DIR__ . '/Console/'
        );
        $this->addNamespace(
            'TextHyphenation\DataProviders',
            __DIR__ . '/DataProviders/'
        );
        $this->addNamespace(
            'TextHyphenation\Hyphenators',
            __DIR__ . '/Hyphenators/'
        );
        $this->addNamespace(
            'TextHyphenation\Timer',
            __DIR__ . '/Timer/'
        );
        $this->addNamespace(
            'TextHyphenation\Logger',
            __DIR__ . '/Logger/'
        );
        $this->addNamespace(
            'TextHyphenation\Cache',
            __DIR__ . '/Cache/'
        );
    }
}
