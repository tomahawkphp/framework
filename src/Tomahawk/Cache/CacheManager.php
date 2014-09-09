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

class CacheManager implements CacheInterface
{
    /**
     * @var CacheProviderInterface
     */
    protected $cacheProvider;

    public function __construct(CacheProviderInterface $cacheProvider)
    {
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function fetch($id)
    {
        return $this->cacheProvider->fetch($id);
    }

    /**
     * @param $id
     * @param $value
     * @param bool $lifetime
     * @return bool
     */
    public function save($id, $value, $lifetime = false)
    {
        $this->cacheProvider->save($id, $value, $lifetime);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function contains($id)
    {
        return $this->cacheProvider->contains($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $this->cacheProvider->delete($id);
    }

    /**
     * @return bool
     */
    public function flush()
    {
        $this->cacheProvider->flush();
    }

    /**
     * @return CacheProviderInterface
     */
    public function getProvider()
    {
        return $this->cacheProvider;
    }

}
