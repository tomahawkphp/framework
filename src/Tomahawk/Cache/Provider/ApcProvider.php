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

use Doctrine\Common\Cache\ApcCache;

class ApcProvider implements CacheProviderInterface
{
    /**
     * @var \Doctrine\Common\Cache\ApcCache
     */
    protected $apcCache;

    public function __construct(ApcCache $apcCache)
    {
        $this->apcCache = $apcCache;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'apc';
    }

    /**
     * @param $id
     * @return mixed
     */
    public function fetch($id)
    {
        return $this->apcCache->fetch($id);
    }

    /**
     * @param $id
     * @param $value
     * @param bool $lifetime
     * @return bool
     */
    public function save($id, $value, $lifetime = false)
    {
        return $this->apcCache->save($id, $value, $lifetime);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function contains($id)
    {
        return $this->apcCache->contains($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->apcCache->delete($id);
    }

    /**
     * @return bool
     */
    public function flush()
    {
        return $this->apcCache->flushAll();
    }
}
