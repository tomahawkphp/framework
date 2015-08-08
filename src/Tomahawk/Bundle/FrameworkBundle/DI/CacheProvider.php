<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\FrameworkBundle\DI;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\Common\Cache\XcacheCache;
use Tomahawk\Cache\CacheManager;
use Tomahawk\Cache\Provider\ApcProvider;
use Tomahawk\Cache\Provider\ArrayProvider;
use Tomahawk\Cache\Provider\FilesystemProvider;
use Tomahawk\Cache\Provider\MemcachedProvider;
use Tomahawk\Cache\Provider\MemcacheProvider;
use Tomahawk\Cache\Provider\RedisProvider;
use Tomahawk\Cache\Provider\XcacheProvider;
use Tomahawk\DI\ServiceProviderInterface;
use Tomahawk\DI\ContainerInterface;

class CacheProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container->set('cache.providers.apc', function(ContainerInterface $c) {
            $cache = new ApcCache();
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return new ApcProvider($cache);
        });

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
            $cache = new MemcachedCache();
            $cache->setMemcached(new \Memcached());
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
            $cache = new RedisCache();
            $cache->setRedis(new \Redis());
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return new RedisProvider($cache);
        });
        //@codeCoverageIgnoreEnd

        $container->set('cache.providers.xcache', function(ContainerInterface $c) {
            $cache = new XcacheCache();
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return new XcacheProvider($cache);
        });

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
