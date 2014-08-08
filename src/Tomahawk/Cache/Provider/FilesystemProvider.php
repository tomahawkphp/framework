<?php

namespace Tomahawk\Cache\Provider;

use Doctrine\Common\Cache\FilesystemCache;

class FilesystemProvider implements CacheProviderInterface
{
    /**
     * @var \Doctrine\Common\Cache\FilesystemCache
     */
    protected $arrayCache;

    public function __construct(FilesystemCache $arrayCache)
    {
        $this->arrayCache = $arrayCache;
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