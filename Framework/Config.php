<?php

namespace Drengr\Framework;

class Config
{
    protected $configDirectory;

    /*
     * This is the list of config files to load. We will keep this list under tight
     * control so that we aren't just loading (executing) any old file someone drops
     * into the config directory. That would be a security risk.
     */
    protected $configFiles = [
        'autoload',
        'bindings',
        'database',
        'router',
        'views',
    ];
    protected $configs = [];

    public function __construct($directory)
    {
        $this->configDirectory =
            rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    public function initialize()
    {
        foreach ($this->configFiles as $configFile) {
            $this->configs[$configFile] = require($this->configDirectory . $configFile . '.php');
        }
    }

    public function get($name)
    {
        $indices = explode('.', $name);
        $configs = $this->configs;

        foreach ($indices as $index) {
            if (isset($configs[$index])) {
                $configs = $configs[$index];
            } else {
                return null;
            }
        }

        return $configs;
    }

    public function set($name, $value)
    {
        $this->configs[$name] = $value;
    }
}
