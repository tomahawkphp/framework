<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Session;

interface SessionInterfaceIdea
{
    /**
     * Set a value in the session
     *
     * @param $name
     * @param $value
     * @return $this
     */
    public function set($name, $value);

    /**
     * Get a value off the session
     *
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * Check if a session value exists
     *
     * @param $name
     * @return bool
     */
    public function has($name);

    /**
     * Remove a value from the session
     *
     * @param $name
     * @return $this
     */
    public function remove($name);

    /**
     * Save Session
     *
     * @return $this
     */
    public function save();
}
