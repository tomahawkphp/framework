<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Cache\Provider;

use Tomahawk\Cache\CacheInterface;

interface CacheProviderInterface extends CacheInterface
{
    /**
     * @return string
     */
    public function getName();
}
