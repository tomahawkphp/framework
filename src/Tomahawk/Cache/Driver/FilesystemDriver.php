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

use Doctrine\Common\Cache\FilesystemCache;

/**
 * Class FilesystemDriver
 * @package Tomahawk\Cache\Driver
 */
class FilesystemDriver implements CacheDriverInterface
{
    /**
     * @var \Doctrine\Common\Cache\FilesystemCache
     */
    protected $filesystemCache;

    public function __construct(FilesystemCache $filesystemCache)
    {
        $this->filesystemCache = $filesystemCache;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'filesystem';
    }

    /**
     * @param $id
     * @return mixed
     */
    public function fetch($id)
    {
        return $this->filesystemCache->fetch($id);
    }

    /**
     * @param $id
     * @param $value
     * @param bool $lifetime
     * @return bool
     */
    public function save($id, $value, $lifetime = false)
    {
        return $this->filesystemCache->save($id, $value, $lifetime);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function contains($id)
    {
        return $this->filesystemCache->contains($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->filesystemCache->delete($id);
    }

    /**
     * @return bool
     */
    public function flush()
    {
        return $this->filesystemCache->flushAll();
    }
}
