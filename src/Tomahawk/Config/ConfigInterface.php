<?php

namespace Tomahawk\Config;


interface ConfigInterface
{
    /**
     * @param null $key
     * @param null $default
     * @return array|null
     */
    public function get($key = null, $default = null);

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value);
}

