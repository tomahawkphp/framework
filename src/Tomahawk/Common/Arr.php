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

class Arr
{
    /**
     * @param array $array Array to look in
     * @param $key
     * @return array
     */
    public static function pluck(array $array, $key)
    {
        return array_map(function($object) use ($key) {
            return is_object($object) ? $object->$key : $object[$key];
        }, $array);
    }

    /**
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
}