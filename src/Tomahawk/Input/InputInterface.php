<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Input;

interface InputInterface
{
    /**
     * Get a value from the query string
     *
     * @param null $key
     * @param null $default
     * @return array|mixed|null
     */
    public function get($key = null, $default = null);

    /**
     * Check if a value exists from the query string
     *
     * @param $key
     * @return bool
     */
    public function getHas($key);

    /**
     * Get values from the query string except the one(s) passed
     *
     * @param $values
     * @return array
     */
    public function getExcept($values);

    /**
     * Get certain values from the query string
     *
     * @param $values
     * @return array
     */
    public function getOnly($values);

    /**
     * Get a value from the request
     *
     * @param null $key
     * @param null $default
     * @return array|mixed|null
     */
    public function post($key = null, $default = null);

    /**
     * Check if a value exists on the request
     *
     * @param $key
     * @return bool
     */
    public function postHas($key);

    /**
     * Get all values from the request except the one(s) passed
     *
     * @param $values
     * @return array
     */
    public function postExcept($values);

    /**
     * Get certain values from the request
     *
     * @param $values
     * @return array
     */
    public function postOnly($values);

    /**
     * Get old input
     *
     * @param $name
     * @return mixed
     */
    public function hasOld($name);

    /**
     * Get a value from the old input bag
     *
     * @param null $name
     * @param null $default
     * @return array|mixed
     */
    public function old($name = null, $default = null);

    /**
     * Flash input for use on next request
     *
     * @param array $data
     * @return $this
     */
    public function flashInput(array $data);
}
