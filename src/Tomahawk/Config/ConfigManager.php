<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Config;

use Tomahawk\Common\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Config\Loader\LoaderInterface;

class ConfigManager implements ConfigInterface
{
    /**
     * @var \Symfony\Component\Config\Loader\LoaderInterface
     */
    protected $loader;

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

    /**
     * @var array
     */
    protected $parsed = array();

    /**
     * @var string
     */
    protected $cacheFile;

    public function __construct(LoaderInterface $loader, $configDirectories, $cacheFile = null)
    {
        $this->loader = $loader;
        $this->configDirectories = $configDirectories;
        $this->cacheFile = $cacheFile;
    }

    /**
     * @param null $key
     * @param null $default
     * @return array|null
     */
    public function get($key = null, $default = null)
    {
        return $this->arrayGet($this->config, $key, $default);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->arraySet($this->config, $key, $value);
        return $this;
    }

    public function load()
    {
        $this->config = array();

        // Check if we have a compiled cached file
        if ($this->cacheFile && file_exists($this->cacheFile)) {
            $this->config = include($this->cacheFile);
            return;
        }

        foreach ($this->configDirectories as $configDirectory) {

            $finder = new Finder();
            $finder->in($configDirectory)->depth('== 0')->files()->name('*.php');

            foreach ($finder as $file) {
                /**
                 * @var \Symfony\Component\Finder\SplFileInfo $file
                 */

                // If file starts with config_ its a compiled one so ignore
                if (Str::is($file->getFilename(), '*config_*')) {
                    continue;
                }

                $key = substr($file->getFilename(), 0, -4);
                $values = $this->loader->load($file->getRealPath());
                $this->set($key, $values);
            }
        }

    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     */
    protected function arraySet(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if ( ! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = array();
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    protected function arrayGet($array, $key = null, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if ( ! is_array($array) || ! array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }

        return $array;
    }
}
