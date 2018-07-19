<?php

namespace Loader;

class Autoloader
{
    private $directories;
    public const NAMESPACE_SEPARATOR = '\\';

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
        $nameSpaces = explode(self::NAMESPACE_SEPARATOR, $class);
        $className = array_pop($nameSpaces);
        $additionalNamespace = '';
        while (!empty($nameSpaces)) {
            $nameSpace = implode(self::NAMESPACE_SEPARATOR, $nameSpaces) . self::NAMESPACE_SEPARATOR;
            if (isset($this->directories[$nameSpace])) {
                foreach ($this->directories[$nameSpace] as $directory) {
                    $file = $directory . DIRECTORY_SEPARATOR . $additionalNamespace . $className . '.php';
                    if (file_exists($file)) {
                        include $file;
                        return $file;
                    }
                }
            }
            $additionalNamespace .= array_pop($nameSpaces) . self::NAMESPACE_SEPARATOR;
        }
        return false;
    }

    /**
     * @param string $prefix
     * @param string $directory
     */
    public function addNameSpace(string $prefix, string $directory): void
    {
        $this->directories[$prefix][] = str_replace(DIRECTORY_SEPARATOR, self::NAMESPACE_SEPARATOR, $directory);
    }

    public function addNameSpaces(array $namespaces): void
    {
        foreach ($namespaces as $key => $value) {
            $this->addNameSpace($key, __DIR__ . $value);
        }
    }
}
