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

use Doctrine\Common\Cache\XcacheCache;
use Tomahawk\Cache\Provider\CacheProviderInterface;

class XcacheProvider implements CacheProviderInterface
{
    /**
     * @var \Doctrine\Common\Cache\XcacheCache
     */
    protected $xcacheCache;

    public function __construct(XcacheCache $xcacheCache)
    {
        $this->xcacheCache = $xcacheCache;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'xcache';
    }

    /**
     * @param $id
     * @return mixed
     */
    public function fetch($id)
    {
        return $this->xcacheCache->fetch($id);
    }

    /**
     * @param $id
     * @param $value
     * @param bool $lifetime
     * @return bool
     */
    public function save($id, $value, $lifetime = false)
    {
        return $this->xcacheCache->save($id, $value, $lifetime);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function contains($id)
    {
        return $this->xcacheCache->contains($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->xcacheCache->delete($id);
    }

    /**
     * @return bool
     */
    public function flush()
    {
        return $this->xcacheCache->flushAll();
    }
}