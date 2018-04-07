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

use Tomahawk\Cache\Provider\CacheProviderInterface;

/**
 * Class CacheManager
 * @package Tomahawk\Cache
 */
class CacheManager implements CacheInterface
{
    /**
     * @var CacheProviderInterface
     */
    protected $cacheProvider;

    /**
     * @param CacheProviderInterface $cacheProvider
     */
    public function __construct(CacheProviderInterface $cacheProvider)
    {
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * Fetch an item out of the cache
     *
     * @param $id
     * @return mixed
     */
    public function fetch($id)
    {
        return $this->cacheProvider->fetch($id);
    }

    /**
     * Save an item into the cache
     *
     * @param $id
     * @param $value
     * @param bool $lifetime
     * @return bool
     */
    public function save($id, $value, $lifetime = false)
    {
        return $this->cacheProvider->save($id, $value, $lifetime);
    }

    /**
     * Check if an item has been cached
     *
     * @param $id
     * @return mixed
     */
    public function contains($id)
    {
        return $this->cacheProvider->contains($id);
    }

    /**
     * Delete an item from the cache
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->cacheProvider->delete($id);
    }

    /**
     * Delete all items from the cache
     *
     * @return bool
     */
    public function flush()
    {
        return $this->cacheProvider->flush();
    }

    /**
     * @return CacheProviderInterface
     */
    public function getProvider()
    {
        return $this->cacheProvider;
    }

}
