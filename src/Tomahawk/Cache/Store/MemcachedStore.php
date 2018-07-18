<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Cache\Store;

use Doctrine\Common\Cache\MemcachedCache;

/**
 * Class MemcachedDriver
 * @package Tomahawk\Cache\Provider
 */
class MemcachedStore implements CacheStoreInterface
{
    use DoctrineTrait;

    public function __construct(MemcachedCache $memcacheCached)
    {
        $this->driver = $memcacheCached;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'memcached';
    }
}
