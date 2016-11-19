<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Routing;

use Symfony\Component\Routing\Route as BaseRoute;

/**
 * Class Route
 *
 * Base on Symfony Route, but for route collections with prefixes
 * Tomahawk strips off the end slash to normalise rout matching based on URL
 *
 * @package Tomahawk\Routing
 */
class Route extends BaseRoute
{
    /**
     * Set a default for a parameter
     *
     * @param $name
     * @param $value
     * @return $this
     */
    public function setDefaultParameter($name, $value)
    {
        return $this->setDefault($name, $value);
    }

    /**
     * Set route requires HTTP
     *
     * @return $this
     */
    public function requiresHttp()
    {
        return $this->setSchemes('http');
    }

    /**
     * Set route requires HTTPS
     *
     * @return $this
     */
    public function requiresHttps()
    {
        return $this->setSchemes('https');
    }

    /**
     * Set a pattern for a parameter
     *
     * @param $parameter
     * @param $pattern
     * @return $this
     */
    public function where($parameter, $pattern)
    {
        return $this->setRequirement($parameter, $pattern);
    }

    public function setPath($pattern)
    {
        return parent::setPath($this->formatPath($pattern));
    }

    /**
     * Format Request Path
     *
     * @param $path
     * @return string
     */
    private function formatPath( $path )
    {
        return '/' .trim(trim($path), '/');
    }
}
