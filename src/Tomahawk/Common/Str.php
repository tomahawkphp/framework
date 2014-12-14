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

use Patchwork\Utf8;

/**
 * Class Str
 *
 * A simple class for string manipulation
 *
 * @package Tomahawk\Common
 */
class Str
{
    public static $random = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * Generic UTF-8 to ASCII transliteration
     *
     * @param  string  $value
     * @return string
     */
    public static function ascii($value)
    {
        return Utf8::toAscii($value);
    }

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
     * Convert string to camel case
     *
     * @param $value
     * @return string
     */
    public static function camelCase($value)
    {
        return lcfirst(static::studlyCase($value));
    }

    /**
     * Convert a value to studly case.
     *
     * @param $value
     * @return string
     */
    public static function studlyCase($value)
    {
        $value = ucwords(str_replace(array('-', '_'), ' ', $value));
        return str_replace(' ', '', $value);
    }

    /**
     * Check if a string matches passed pattern
     *
     * @param $value
     * @param $pattern
     * @return bool
     */
    public static function is($value, $pattern)
    {
        if ($pattern === $value) {
            return true;
        }

        $pattern = preg_quote($pattern, '/');

        $pattern = str_replace('\*', '.*', $pattern).'\z';

        return (bool) preg_match('/^'.$pattern.'/', $value);
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * Taken from \Illuminate\Support\Str
     *
     * @param  string  $title
     * @param  string  $separator
     * @return string
     */
    public static function slug($title, $separator = '-')
    {
        $title = static::ascii($title);

        // Convert all dashes/underscores into separator
        $flip = $separator == '-' ? '_' : '-';

        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', mb_strtolower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        return trim($title, $separator);
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
