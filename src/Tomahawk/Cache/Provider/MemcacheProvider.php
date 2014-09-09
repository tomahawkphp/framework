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

use Doctrine\Common\Cache\MemcacheCache;
use Tomahawk\Cache\Provider\CacheProviderInterface;

class MemcacheProvider implements CacheProviderInterface
{
    /**
     * @var \Doctrine\Common\Cache\MemcacheCache
     */
    protected $memcacheCache;

    public function __construct(MemcacheCache $memcacheCache)
    {
        $this->memcacheCache = $memcacheCache;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'memcache';
    }

    /**
     * @param $id
     * @return mixed
     */
    public function fetch($id)
    {
        return $this->memcacheCache->fetch($id);
    }

    /**
     * @param $id
     * @param $value
     * @param bool $lifetime
     * @return bool
     */
    public function save($id, $value, $lifetime = false)
    {
        return $this->memcacheCache->save($id, $value, $lifetime);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function contains($id)
    {
        return $this->memcacheCache->contains($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->memcacheCache->delete($id);
    }

    /**
     * @return bool
     */
    public function flush()
    {
        return $this->memcacheCache->flushAll();
    }
}
