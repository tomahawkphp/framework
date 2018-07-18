<?php

namespace Tomahawk\Cache\Factory;

use Doctrine\Common\Cache\MemcachedCache;
use InvalidArgumentException;
use Memcached;
use Predis\Client;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\PredisCache;
use Doctrine\Common\Cache\ArrayCache;
use Psr\Container\ContainerInterface;
use Tomahawk\Cache\CacheInterface;
use Tomahawk\Cache\Store\ArrayStore;
use Tomahawk\Cache\Store\FilesystemStore;
use Tomahawk\Cache\Store\MemcachedStore;
use Tomahawk\Cache\Store\RedisStore;
use Tomahawk\Config\ConfigInterface;

/**
 * Class StoreFactory
 * @package Tomahawk\Cache\Factory
 */
class StoreFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $createdDrivers = [];

    /**
     * @var array
     */
    protected $customDrivers;

    /**
     * @var ConfigInterface
     */
    protected $config;

    public function __construct(
        ContainerInterface $container,
        ConfigInterface $config,
        array $customDrivers = []
    )
    {
        $this->container = $container;
        $this->config = $config;
        $this->customDrivers = $customDrivers;
    }

    /**
     * @param string $name
     * @return CacheInterface
     */
    public function make(string $name)
    {
        return $this->createDriver($name);
    }

    /**
     * @param string $name
     * @throws InvalidArgumentException
     * @return CacheInterface
     */
    protected function createDriver(string $name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Cache driver [{$name}] is not defined.");
        }

        if (isset($this->customDrivers[$config['driver']])) {
            return $this->callCustomCreator($name, $config);
        }

        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($name, $config);
        }

        throw new InvalidArgumentException("Cache driver [{$name}] is not defined.");
    }

    /**
     * @param string $name
     * @param array $config
     * @return CacheInterface
     */
    protected function callCustomCreator(string $name, array $config)
    {
        $driver = $this->customDrivers[$config['driver']]->make($name, $config);

        return $this->createdDrivers[$name] = $driver;
    }

    /**
     * @param string $name
     * @param array $config
     * @return CacheInterface
     */
    protected function createArrayDriver(string $name, array $config)
    {
        $prefix = $this->config->get('cache.prefix', '');
        $cache = new ArrayCache();
        $cache->setNamespace($prefix);
        $driver = new ArrayStore($cache);

        return $this->createdDrivers[$name] = $driver;
    }

    /**
     * @param string $name
     * @param array $config
     * @return CacheInterface
     */
    protected function createFilesystemDriver(string $name, array $config)
    {
        $prefix = $this->config->get('cache.prefix', '');
        $cache = new FilesystemCache($config['directory']);
        $cache->setNamespace($prefix);
        $driver = new FilesystemStore($cache);

        return $this->createdDrivers[$name] = $driver;
    }

    /**
     * @param string $name
     * @param array $config
     * @return CacheInterface
     */
    protected function createRedisDriver(string $name, array $config)
    {
        $prefix = $this->config->get('cache.prefix', '');
        $redisConfig = $this->config->get('database.redis');

        $client = new Client($redisConfig[$config['connection']], $redisConfig['options']);

        $cache = new PredisCache($client);

        $cache->setNamespace($prefix);
        $driver = new RedisStore($cache);

        return $this->createdDrivers[$name] = $driver;
    }

    /**
     * @param string $name
     * @param array $config
     * @return CacheInterface
     */
    protected function createMemcachedDriver(string $name, array $config)
    {
        $prefix = $this->config->get('cache.prefix', '');

        if ($config['persistent_id']) {
            $memcached = new \Memcached($config['persistent_id']);
        }
        else {
            $memcached = new \Memcached();
        }

        if (count($config['options'])) {
            $memcached->setOptions($config['options']);
        }

        $credentials = array_filter($config['sasl']);

        if (2 === count($credentials)) {

            list($username, $password) = $credentials;

            $memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, true);

            $memcached->setSaslAuthData($username, $password);
        }

        if ( ! $memcached->getServerList()) {
            foreach ($config['servers'] as $server) {
                $memcached->addServer(
                    $server['host'], $server['port'], $server['weight']
                );
            }
        }

        $cache = new MemcachedCache();
        $cache->setMemcached($memcached);

        $cache->setNamespace($prefix);
        $driver = new MemcachedStore($cache);

        return $this->createdDrivers[$name] = $driver;
    }

    /**
     * @param string $name
     * @return array|null
     */
    protected function getConfig(string $name)
    {
        return $this->config->get("cache.stores.{$name}");
    }
}
