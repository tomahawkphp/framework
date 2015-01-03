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

use Tomahawk\Common\Str;

class TestOpenStr extends Str
{
    protected static function supportOpenSSL()
    {
        return true;
    }

    protected static function secureBytes($length)
    {
        return false;
    }
}
