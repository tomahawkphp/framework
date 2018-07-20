<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Cache;

/**
 * Interface CacheManagerInterface
 * @package Tomahawk\Cache
 */
interface CacheManagerInterface extends CacheInterface
{
    /**
     * @param string|null $name
     * @return CacheInterface
     */
    public function driver(string $name = null);
}
