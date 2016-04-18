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

use Doctrine\Common\Cache\ApcuCache;

class ApcuProvider implements CacheProviderInterface
{
    /**
     * @var \Doctrine\Common\Cache\ApcuCache
     */
    protected $apcuCache;

    public function __construct(ApcuCache $apcuCache)
    {
        $this->apcuCache = $apcuCache;
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
        return $this->apcuCache->fetch($id);
    }

    /**
     * @param $id
     * @param $value
     * @param bool $lifetime
     * @return bool
     */
    public function save($id, $value, $lifetime = false)
    {
        return $this->apcuCache->save($id, $value, $lifetime);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function contains($id)
    {
        return $this->apcuCache->contains($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->apcuCache->delete($id);
    }

    /**
     * @return bool
     */
    public function flush()
    {
        return $this->apcuCache->flushAll();
    }
}
