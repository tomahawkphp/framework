<?php

namespace Tomahawk\Common;


class Str
{
    /**
     *
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
}