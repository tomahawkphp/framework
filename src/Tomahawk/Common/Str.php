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

class Str
{
    public static $random = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    /**
     * Convert the given string to lower-case.
     *
     * @param  string  $value
     * @return string
     */
    public static function lower($value)
    {
        return mb_strtolower($value);
    }

    /**
     * Convert the given string to upper-case.
     *
     * @param  string  $value
     * @return string
     */
    public static function upper($value)
    {
        return mb_strtoupper($value);
    }

    /**
     * Convert the given string to title case.
     *
     * @param  string  $value
     * @return string
     */
    public static function title($value)
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Check if string starts with passed search string(s)
     *
     * @param $string
     * @param $search
     * @return bool
     */
    public static function startsWith($string, $search)
    {
        if (!is_array($search)) {
            $search = array($search);
        }

        foreach ($search as $item) {
            if (0 === strpos($string, $item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if string ends with passed search string(s)
     *
     * @param $string
     * @param $search
     * @return bool
     */
    public static function endsWith($string, $search)
    {
        if (!is_array($search)) {
            $search = array($search);
        }

        foreach ($search as $item) {
            if ((string) $item === substr($string, -strlen($item))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int $length
     * @return string
     */
    public static function random($length = 16)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length * 2);

            if (false !== $bytes) {
                return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
            }
        }
        return static::quickRandom($length);
    }

    /**
     * Generate a "random" alpha-numeric string.
     *
     * Should not be considered sufficient for cryptography, etc.
     *
     * @param  int  $length
     * @return string
     */
    public static function quickRandom($length = 16)
    {
        return substr(str_shuffle(str_repeat(static::$random, 5)), 0, $length);
    }
}
