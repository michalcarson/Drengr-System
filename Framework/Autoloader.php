<?php

namespace Drengr\Framework;

class Autoloader
{
    protected static $map;

    public static function initialize()
    {
        spl_autoload_register([self::class, 'loader']);
        $map = require (__DIR__ . '/../config/autoload.php');

        // Pre-process the directory names before we add them to the class map.
        // Each directory needs to be prefixed with our plugin directory. All
        // classes will be loaded from below that directory.
        $dirs = explode(DIRECTORY_SEPARATOR, __DIR__);
        array_pop($dirs); // remove the lowest level directory
        $parentDirectory = implode(DIRECTORY_SEPARATOR, $dirs);

        foreach ($map as $namespace => $subdir) {
            $dir = $parentDirectory . DIRECTORY_SEPARATOR . $subdir;
            self::$map[$namespace] = $dir;
        }
    }

    public static function loader($classname)
    {
        $loader = function ($classname, $namespace, $dir) {
            if (strpos($classname, $namespace) !== false) {
                $file = $dir .
                    str_replace(
                        [$namespace . '\\', '\\'],
                        ['', DIRECTORY_SEPARATOR],
                        $classname
                    ) .
                    '.php';

                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            return false;
        };

        foreach (self::$map as $namespace => $dir) {
            if ($loader($classname, $namespace, $dir)) {
                return;
            }
        }
    }
}
