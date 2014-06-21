<?php

namespace Tomahawk\Config;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class ConfigManager implements ConfigInterface
{
    /**
     * @var \Symfony\Component\Finder\Finder
     */
    protected $finder;

    /**
     * @var
     */
    protected $configDirectories;

    /**
     * @var array
     */
    protected $config = array();

    public function __construct($configDirectories)
    {
        $this->configDirectories = $configDirectories;
    }

    /**
     * @param null $key
     * @param null $default
     * @return array|null
     */
    public function get($key = null, $default = null)
    {
        if (is_null($key))
        {
            return $this->config;
        }
        return $this->getValue($key, $default);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->setValue($key, $value);
        return $this;
    }

    public function save($directory)
    {
        foreach ($this->config as $key => $config)
        {
            if (!is_dir($directory))
            {
                mkdir($directory);
            }

            $file = $directory . $key . '.php';

            //file_put_contents($file, sprintf("<?php \n\nreturn %s;",json_encode($config)));

            $string = '<?php return ' . var_export($config, true) . ';';

            file_put_contents($file, $string);
        }
    }

    /**
     * @param $key
     * @param $filename
     */
    public function loadFromFile($key, $filename)
    {
        $config = require_once($filename);
        $this->setValue($key, $config);
    }

    public function load()
    {
        foreach ($this->configDirectories as $configDirectory)
        {
            $finder = new Finder();
            $finder->in($configDirectory)->depth('== 0')->files()->name('*.php');

            foreach ($finder as $file) {
                /**
                 * @var \Symfony\Component\Finder\SplFileInfo $file
                 */
                $key = substr($file->getFilename(), 0, -4);
                $this->loadFromFile($key, $file->getRealPath());
            }
        }

    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param mixed $configDirectory
     */
    public function setConfigDirectory($configDirectory)
    {
        $this->configDirectory = $configDirectory;
    }

    /**
     * @return mixed
     */
    public function getConfigDirectory()
    {
        return $this->configDirectory;
    }

    /**
     * @param \Symfony\Component\Finder\Finder $finder
     */
    public function setFinder($finder)
    {
        $this->finder = $finder;
    }

    /**
     * @return \Symfony\Component\Finder\Finder
     */
    public function getFinder()
    {
        return $this->finder;
    }

    /**
     * @param $key
     * @param null $default
     * @return array|null
     */
    protected function getValue($key, $default = null)
    {
        $array = $this->config;

        if (is_null($key)) {
            return $array;
        }

        // To retrieve the array item using dot syntax, we'll iterate through
        // each segment in the key and look for that value. If it exists, we
        // will return it, otherwise we will set the depth of the array and
        // look for the next segment.
        foreach (explode('.', $key) as $segment)
        {
            if ( ! is_array($array) or ! array_key_exists($segment, $array))
            {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    protected function setValue($key, $value)
    {
        if (is_null($key)) {
            return $this->config = $value;
        }

        $keys = explode('.', $key);

        // This loop allows us to dig down into the array to a dynamic depth by
        // setting the array value for each level that we dig into. Once there
        // is one key left, we can fall out of the loop and set the value as
        // we should be at the proper depth.
        while (count($keys) > 1)
        {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an
            // empty array to hold the next value, allowing us to create the
            // arrays to hold the final value.
            if ( ! isset($this->config[$key]) or ! is_array($this->config[$key]))
            {
                $this->config[$key] = array();
            }

            $this->config =& $this->config[$key];
        }

        $this->config[array_shift($keys)] = $value;
    }
}
