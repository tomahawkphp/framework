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

interface SessionInterface
{
    /**
     * Get session storage
     *
     * @return \Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface
     */
    public function getStorage();

    /**
     * Get old input bag
     *
     * @return \Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface
     */
    public function getOldInputBag();

    /**
     * Put value in old input bag
     *
     * @param $name
     * @param $value
     */
    public function setOldInput($name, $value);

    /**
     * Get value from old input bag
     *
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function getOldInput($name, $default = null);

    /**
     * Check if a value is in the old input bag
     *
     * @param $name
     * @return bool
     */
    public function hasOldInput($name);

    /**
     * Get default session bag
     *
     * @return \Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface
     */
    public function getNewInputBag();

    /**
     * Put a value in the new input bag
     *
     * @param $name
     * @param $value
     */
    public function setNewInput($name, $value);

    /**
     * Get a value from the new input bag
     *
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function getNewInput($name, $default = null);

    /**
     * Check if a value is in the new input bag
     *
     * @param $name
     * @return bool
     */
    public function hasNewInput($name);

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
     * Check if flash value exists
     *
     * @param $name
     * @return bool
     */
    public function hasFlash($name);

    /**
     * Get flash values
     *
     * @param $name
     * @param array $default
     * @return array
     */
    public function getFlash($name, $default = array());

    /**
     * Flash a value
     *
     * @param $name
     * @param $value
     * @return $this
     */
    public function setFlash($name, $value);

    /**
     * Get Flash Bag
     *
     * @return \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface
     */
    public function getFlashBag();

    /**
     * Check if session is dirty
     *
     * @return bool
     */
    public function isDirty();

    /**
     * Get input from both new and old input bags
     *
     * @return array
     */
    public function getInputData();

    /**
     * Reflash old input to use on next request
     *
     * @return $this
     */
    public function reflashInput();

    /**
     * Clear new input
     *
     * @return $this
     */
    public function clearNewInput();

    /**
     * Clear old input
     *
     * @return $this
     */
    public function clearOldInput();

    /**
     * Merge new input into old input bag
     *
     * @return $this
     */
    public function mergeNewInput();

    /**
     * Save Session
     *
     * @return $this
     */
    public function save();

}
