<?php

namespace Tomahawk\Cache\DependencyInjection;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\Common\Cache\RedisCache;
use Tomahawk\Cache\CacheManager;
use Tomahawk\Cache\CacheManagerInterface;
use Tomahawk\Cache\Factory\StoreFactory;
use Tomahawk\Cache\Provider\ArrayProvider;
use Tomahawk\Cache\Provider\FilesystemProvider;
use Tomahawk\Cache\Provider\MemcachedProvider;
use Tomahawk\Cache\Provider\MemcacheProvider;
use Tomahawk\Cache\Provider\RedisProvider;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\DependencyInjection\ContainerInterface;

/**
 * Class CacheServiceProvider
 * @package Tomahawk\Cache\DependencyInjection
 */
class CacheServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param ContainerInterface $container An Container instance
     */
    public function register(ContainerInterface $container)
    {
        $container->set(StoreFactory::class, function(ContainerInterface $c) {

            $storeFactoryServiceIds = $c->findTaggedServiceIds('cache.store.factory');

            $customFactories = [];

            foreach ($storeFactoryServiceIds as $factoryServiceId) {
                $customFactory = $c->get($factoryServiceId);
                $customFactories[$customFactory->getName()] = $customFactory;
            }

            return new StoreFactory(
                $c,
                $c->get(ConfigInterface::class),
                $customFactories
            );
        });

        $container->set(CacheManagerInterface::class, function(ContainerInterface $c) {
            $default = $c->get(ConfigInterface::class)->get('cache.default', 'array');
            return new CacheManager(
                $c->get(StoreFactory::class),
                $default
            );
        });
    }
}
