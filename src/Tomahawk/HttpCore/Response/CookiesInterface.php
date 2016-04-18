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

use Symfony\Component\HttpFoundation\Cookie;

interface CookiesInterface
{
    /**
     * Check if a cookie exists
     *
     * @param $name
     * @return bool
     */
    public function has($name);

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
    public function set($name, $value, $minutes = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true);

    /**
     * Get cookie value
     *
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function get($name, $default = null);

    /**
     * Expire Cookie
     *
     * @param $name
     */
    public function expire($name);

    /**
     * Get queued cookies
     *
     * @return Cookie[]
     */
    public function getQueued();
}
