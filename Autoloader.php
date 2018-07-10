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

    /**
     * @param string $class
     * @return bool|string
     */
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

    /**
     * @param string $prefix
     * @param string $directory
     */
    public function addNameSpace(string $prefix, string $directory) : void
    {
        $this->directories[$prefix][] = $directory;
    }

    public function addNameSpaces(array $namespaces) : void
    {
        foreach ($namespaces as $key => $value) {
            $this->addNameSpace($key, __DIR__ . DIRECTORY_SEPARATOR . $value);
        }
    }
}
