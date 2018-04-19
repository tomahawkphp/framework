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

interface ConfigInterface
{
    /**
     * @param null $key
     * @param null $default
     * @return mixed|null
     */
    public function get($key = null, $default = null);

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value);

    /**
     * Check if a config value exists
     *
     * @param $key
     * @return bool
     */
    public function has($key);

    /**
     * Load config files
     *
     * @param bool|false $force
     */
    public function load($force = false);
}
