<?php

namespace Tomahawk\Cache;

use Memcache;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\XcacheCache;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\RedisCache;

class CacheManager implements CacheInterface
{
    /**
     * @var \Doctrine\Common\Cache\CacheProvider
     */
    protected $driver;

    public function __construct(array $config)
    {
        switch($config['driver'])
        {
            case 'filesystem':
                $this->setupFileSystem($config);
                break;
            case 'apc':
                $this->setupApcCache($config);
                break;
            case 'xcache':
                $this->setupXCache($config);
                break;
            case 'redis':
                $this->setupRedis($config);
                break;
            case 'array':
                $this->setupArray($config);
                break;
            case 'memcache':
                $this->setMemcacheCache($config);
                break;
            default:
                $this->setupArray($config);
                break;
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function fetch($id)
    {
        return $this->driver->fetch($id);
    }

    /**
     * @param $id
     * @param $value
     * @param bool $lifetime
     * @return bool
     */
    public function save($id, $value, $lifetime = false)
    {
        return $this->driver->save($id, $value, $lifetime);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function contains($id)
    {
        return $this->driver->contains($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->driver->delete($id);
    }

    /**
     * @return bool
     */
    public function flush()
    {
        return $this->driver->flushAll();
    }


    /**
     * @param array $config
     */
    protected function setupFileSystem(array $config)
    {
        $this->driver = new FilesystemCache($config['save_path']);
    }

    protected function setupArray(array $config)
    {
        $this->driver = new ArrayCache();
    }

    protected function setupRedis(array $config)
    {
        //$redis = new Redis;
        $this->driver = new RedisCache();
        //$this->driver->setRedis($redis);
    }

    protected function setupXCache(array $config)
    {
        $this->driver = new XcacheCache();
    }

    protected function setupApcCache(array $config)
    {
        $this->driver = new ApcCache();
    }

    /*
    protected function setMemcacheCache(array $config)
    {
        $memcache = new Memcache();
        $memcache->connect($config['memcache']['host'], $config['memcache']['port']);

        $this->driver = new MemcacheCache();
        $this->driver->setMemcache($memcache);
    }*/
}