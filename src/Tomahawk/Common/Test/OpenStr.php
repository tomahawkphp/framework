<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Common\Test;

use Tomahawk\Common\Str as BaseStr;

class OpenStr extends BaseStr
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
