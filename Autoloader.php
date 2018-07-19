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
        $additionalNamespace = '';
        while (!empty($nameSpaces)) {
            $nameSpace = implode('\\', $nameSpaces) . '\\';
            if (isset($this->directories[$nameSpace])) {
                foreach ($this->directories[$nameSpace] as $directory) {
                    $file = $directory . DIRECTORY_SEPARATOR . $additionalNamespace . $className . '.php';
                    if (file_exists($file)) {
                        include $file;
                        return $file;
                    }
                }
            }
            $additionalNamespace .= array_pop($nameSpaces) . '\\';
        }
        return false;
    }

    /**
     * @param string $prefix
     * @param string $directory
     */
    public function addNameSpace(string $prefix, string $directory): void
    {
        $this->directories[$prefix][] = str_replace('/', '\\', $directory);
    }

    public function addNameSpaces(array $namespaces): void
    {
        foreach ($namespaces as $key => $value) {
            $this->addNameSpace($key, __DIR__ . $value);
        }
    }
}
