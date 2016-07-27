<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\HttpCore\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Class Cookies
 *
 * Simple container for cookies that will be added to the current request
 *
 * @package Tomahawk\HttpCore\Response
 */
class Cookies implements CookiesInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Cookie[]
     */
    protected $cookies = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Check if a cookie exists
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return $this->request->cookies->has($name);
    }

    /**
     * Set a new cookies into the collection
     *
     * @param $name
     * @param $value
     * @param int $minutes
     * @param string $path
     * @param null $domain
     * @param bool|false $secure
     * @param bool|true $httpOnly
     * @return $this
     */
    public function set($name, $value, $minutes = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true)
    {
        $expire = ($minutes == 0) ? 0 : time() + ($minutes * 60);

        $cookie = new Cookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);

        $this->cookies[$name] = $cookie;

        return $this;
    }

    /**
     * Get cookie value
     *
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function get($name, $default = null)
    {
        if (null !== ($value = $this->request->cookies->get($name))) {
            return $value ?: $default;
        }

        return $default;
    }

    /**
     * Expire Cookie
     *
     * @param $name
     */
    public function expire($name)
    {
        $this->request->cookies->remove($name);
    }

    /**
     * Get queued cookies
     *
     * @return Cookie[]
     */
    public function getQueued()
    {
        return $this->cookies;
    }
}
