<?php

namespace Tomahawk\Cache\Provider;

use Doctrine\Common\Cache\ApcCache;

class ApcProvider extends CacheProvider
{
    /**
     * @var \Doctrine\Common\Cache\ApcCache
     */
    protected $arrayCache;

    public function __construct(ApcCache $arrayCache)
    {
        $this->arrayCache = $arrayCache;
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