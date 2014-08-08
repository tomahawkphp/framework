<?php

namespace Tomahawk\Cache\Provider;

use Doctrine\Common\Cache\RedisCache;
use Tomahawk\Cache\Provider\CacheProviderInterface;

class RedisProvider implements CacheProviderInterface
{
    /**
     * @var \Doctrine\Common\Cache\RedisCache
     */
    protected $redisCache;

    public function __construct(RedisCache $redisCache)
    {
        $this->redisCache = $redisCache;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'redis';
    }

    /**
     * @param $id
     * @return mixed
     */
    public function fetch($id)
    {
        return $this->redisCache->fetch($id);
    }

    /**
     * @param $id
     * @param $value
     * @param bool $lifetime
     * @return bool
     */
    public function save($id, $value, $lifetime = false)
    {
        return $this->redisCache->save($id, $value, $lifetime);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function contains($id)
    {
        return $this->redisCache->contains($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->redisCache->delete($id);
    }

    /**
     * @return bool
     */
    public function flush()
    {
        return $this->redisCache->flushAll();
    }
}