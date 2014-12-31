<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Common;

use Closure;

/**
 * Class Arr
 *
 * A simple class for array manipulation
 *
 * @package Tomahawk\Common
 */
class Arr
{
    /**
     * Get first value in array without changing pointer
     *
     * @param array $array
     * @return mixed
     */
    public static function first(array $array)
    {
        $clone = $array;
        return reset($clone);
    }

    /**
     * Get first value in array from closure
     *
     * @param array $array
     * @param callable $closure
     * @return mixed
     */
    public static function firstBy(array $array, Closure $closure)
    {
        foreach ($array as $key => $value) {
            if (true === call_user_func_array($closure, array($key, $value))) {
                return $value;
            }
        }
        return null;
    }

    /**
     * Get last value in array without changing pointer
     *
     * @param array $array
     * @return mixed
     */
    public static function last(array $array)
    {
        $clone = $array;
        return end($clone);
    }

    /**
     * Get all elements from array based on keys passed
     *
     * @param array $array
     * @param $keys
     * @return array
     */
    public static function only(array $array, $keys)
    {
        if (!is_array($keys)) {
            $keys = array($keys);
        }

        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * Get all elements from an array except keys passed
     *
     * @param array $array
     * @param $keys
     * @return array
     */
    public static function except(array $array, $keys)
    {
        if (!is_array($keys)) {
            $keys = array($keys);
        }

        return array_diff_key($array, array_flip($keys));
    }

    /**
     * Get value from array if it exists otherwise return default value
     *
     * @param array $array
     * @param $key
     * @param null $default
     * @return null
     */
    public static function get(array $array, $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    /**
     * Set value in array
     *
     * @param array $array
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function set(array &$array, $key, $value)
    {
        return $array[$key] = $value;
    }

    /**
     * Check if an array has a key
     *
     * @param array $array
     * @param $key
     * @return bool
     */
    public static function has(array $array, $key)
    {
        return isset($array[$key]);
    }

    /**
     * Check if an array contains an value
     *
     * @param array $array
     * @param $value
     * @return mixed
     */
    public static function contains(array $array, $value)
    {
        return false !== array_search($value, $array, true);
    }
}
