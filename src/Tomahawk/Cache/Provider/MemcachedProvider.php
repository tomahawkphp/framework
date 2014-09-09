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

use Doctrine\Common\Cache\MemcachedCache;
use Tomahawk\Cache\Provider\CacheProviderInterface;

class MemcachedProvider implements CacheProviderInterface
{
    /**
     * @var \Doctrine\Common\Cache\MemcachedCache
     */
    protected $memcacheCached;

    public function __construct(MemcachedCache $memcacheCached)
    {
        $this->memcacheCached = $memcacheCached;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'memcached';
    }

    /**
     * @param $id
     * @return mixed
     */
    public function fetch($id)
    {
        return $this->memcacheCached->fetch($id);
    }

    /**
     * @param $id
     * @param $value
     * @param bool $lifetime
     * @return bool
     */
    public function save($id, $value, $lifetime = false)
    {
        return $this->memcacheCached->save($id, $value, $lifetime);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function contains($id)
    {
        return $this->memcacheCached->contains($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->memcacheCached->delete($id);
    }

    /**
     * @return bool
     */
    public function flush()
    {
        return $this->memcacheCached->flushAll();
    }
}
