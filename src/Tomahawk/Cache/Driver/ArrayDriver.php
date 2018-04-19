<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Cache\Driver;

use Doctrine\Common\Cache\ArrayCache;

/**
 * Class ArrayDriver
 * @package Tomahawk\Cache\Driver
 */
class ArrayDriver implements CacheDriverInterface
{
    /**
     * @var \Doctrine\Common\Cache\ArrayCache
     */
    protected $arrayCache;

    public function __construct(ArrayCache $arrayCache)
    {
        $this->arrayCache = $arrayCache;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'array';
    }

    /**
     * @param $id
     * @return mixed
     */
    public function fetch($id)
    {
        return $this->arrayCache->fetch($id);
    }

    /**
     * @param $id
     * @param $value
     * @param bool $lifetime
     * @return bool
     */
    public function save($id, $value, $lifetime = false)
    {
        return $this->arrayCache->save($id, $value, $lifetime);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function contains($id)
    {
        return $this->arrayCache->contains($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->arrayCache->delete($id);
    }

    /**
     * @return bool
     */
    public function flush()
    {
        return $this->arrayCache->flushAll();
    }
}
