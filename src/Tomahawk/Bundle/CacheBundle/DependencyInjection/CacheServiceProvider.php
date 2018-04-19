<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\CacheBundle\DependencyInjection;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\Common\Cache\RedisCache;
use Tomahawk\Cache\CacheManager;
use Tomahawk\Cache\Provider\ArrayProvider;
use Tomahawk\Cache\Provider\FilesystemProvider;
use Tomahawk\Cache\Provider\MemcachedProvider;
use Tomahawk\Cache\Provider\MemcacheProvider;
use Tomahawk\Cache\Provider\RedisProvider;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\DependencyInjection\ContainerInterface;

class CacheServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container->set('cache.providers.array', function(ContainerInterface $c) {
            $cache = new ArrayCache();
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return new ArrayProvider($cache);
        });

        $container->set('cache.providers.filesystem', function(ContainerInterface $c) {
            $cache = new FilesystemCache($c['config']->get('cache.directory'));
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return new FilesystemProvider($cache);
        });

        //@codeCoverageIgnoreStart
        $container->set('cache.providers.memcached', function(ContainerInterface $c) {

            $memcached = new \Memcached();
            //$memcached->setOptions()
            //$memcached->addServers()
            //$memcached->setSaslAuthData()

            $cache = new MemcachedCache();
            $cache->setMemcached($memcached);
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return new MemcachedProvider($cache);
        });

        $container->set('cache.providers.memcache', function(ContainerInterface $c) {
            $cache = new MemcacheCache();
            $cache->setMemcache(new \Memcache());
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return new MemcacheProvider($cache);
        });

        $container->set('cache.providers.redis', function(ContainerInterface $c) {

            $c['config']->get('cache.providers', 'array');

            // @TODO - Get redis connection
            $redis = new \Redis();
            //$redis->select();

            $cache = new RedisCache();
            $cache->setRedis($redis);
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return new RedisProvider($cache);
        });
        //@codeCoverageIgnoreEnd

        //@codeCoverageIgnoreStart
        $container->set('Tomahawk\Cache\CacheInterface', function(ContainerInterface $c) {
            $provider = $c['config']->get('cache.driver', 'array');
            if (!$c->has('cache.providers.' .$provider)) {
                throw new \Exception(sprintf('Cache provider %s does not exist or has not been set.', $provider));
            }
            return new CacheManager($c['cache.providers.' .$provider]);
        });
        //@codeCoverageIgnoreEnd

        $container->addAlias('cache', 'Tomahawk\Cache\CacheInterface');
    }
}
