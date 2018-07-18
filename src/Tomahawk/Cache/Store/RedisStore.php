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

use Doctrine\Common\Cache\CacheProvider;

/**
 * Class RedisStore
 * @package Tomahawk\Cache\Driver
 */
class RedisStore implements CacheStoreInterface
{
    use DoctrineTrait;

    public function __construct(CacheProvider $redisCache)
    {
        $this->driver = $redisCache;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'redis';
    }
}
