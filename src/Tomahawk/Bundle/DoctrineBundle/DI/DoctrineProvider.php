<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\DoctrineBundle\DI;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\Common\Cache\XcacheCache;
use Doctrine\DBAL\DriverManager;
use Tomahawk\Bundle\DoctrineBundle\Auth\Handlers\DoctrineAuthHandler;
use Tomahawk\Bundle\DoctrineBundle\Registry;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\DI\ServiceProviderInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class DoctrineProvider implements ServiceProviderInterface
{
    protected $allowedFormats = array(
        'xml',
        'yml',
        'annotations'
    );

    public function register(ContainerInterface $container)
    {
        $this->registerCache($container);
        $this->registerDatabase($container);
    }

    public function registerDatabase(ContainerInterface $container)
    {
        // Get all connections
        $container->set('doctrine.connections', function(ContainerInterface $c) {

            $doctrineConfig = $c->get('config')->get('doctrine');

            $connections = $doctrineConfig['connections'];
            $services = array();

            foreach ($connections as $name => $settings) {
                $services[$name] = $settings['service'];
            }

            return $services;
        });

        $container->set('doctrine', function(ContainerInterface $c) {

            $doctrineConfig = $c->get('config')->get('doctrine');
            $defaultConnection = $doctrineConfig['default_connection'];

            $registry = new Registry($c, $c['doctrine.connections'], array('default' => $c['doctrine.entitymanager']), $defaultConnection, 'default');
            return $registry;
        });

        $container->set('doctrine.entitymanager', function(ContainerInterface $c) {

            $cache = $c['doctrine.cache'];

            $doctrineConfig = $c->get('config')->get('doctrine');

            $config = Setup::createXMLMetadataConfiguration(
                $doctrineConfig['mapping_directories'],
                $c->get('kernel')->isDebug(),
                $doctrineConfig['proxy_directories'],
                $cache
            );

            $config->setProxyNamespace($doctrineConfig['proxy_namespace']);
            $config->setAutoGenerateProxyClasses($doctrineConfig['auto_generate_proxies']);

            return EntityManager::create($c['doctrine.connection.default'], $config);
        });

        $container->set('doctrine.connection.default', function(ContainerInterface $c) {
            $doctrineConfig = $c->get('config')->get('doctrine');

            $defaultConnection = $doctrineConfig['default_connection'];

            $allConnections = $doctrineConfig['connections'];

            if (!isset($allConnections[$defaultConnection])) {
                throw new \InvalidArgumentException(sprintf('Connection %s does not exist', $defaultConnection));
            }

            return DriverManager::getConnection($allConnections[$defaultConnection]);
        });

        $container->set('doctrine_auth_handler', function(ContainerInterface $c) {

            /**
             * @var ConfigInterface $config
             */
            $config = $c['config'];

            $model = $config->get('security.doctrine_model');
            $usernameField = $config->get('security.doctrine_username_field');

            return new DoctrineAuthHandler($c['hasher'], $c['doctrine'], $model, $usernameField);
        });
    }

    public function registerCache(ContainerInterface $container)
    {
        $container->set('doctrine.cache', function(ContainerInterface $c) {
            $doctrineConfig = $c->get('config')->get('doctrine');

            $cacheService = sprintf('doctrine.cache.%s', $doctrineConfig['cache']);
            return $c[$cacheService];
        });

        $container->set('doctrine.cache.apc', function(ContainerInterface $c) {
            $cache = new ApcCache();
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return $cache;
        });

        $container->set('doctrine.cache.array', function(ContainerInterface $c) {
            $cache = new ArrayCache();
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return $cache;
        });

        $container->set('doctrine.cache.filesystem', function(ContainerInterface $c) {
            $config = $c['config'];
            $cache = new FilesystemCache($config->get('cache.directory'));
            $cache->setNamespace($config->get('cache.namespace', ''));
            return $cache;
        });

        $container->set('doctrine.cache.memcached', function(ContainerInterface $c) {
            $cache = new MemcachedCache();
            $cache->setMemcached(new \Memcached());
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return $cache;
        });

        $container->set('doctrine.cache.memcache', function(ContainerInterface $c) {
            $cache = new MemcacheCache();
            $cache->setMemcache(new \Memcache());
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return $cache;
        });

        $container->set('doctrine.cache.redis', function(ContainerInterface $c) {
            $cache = new RedisCache();
            $cache->setRedis(new \Redis());
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return $cache;
        });

        $container->set('doctrine.cache.xcache', function(ContainerInterface $c) {
            $cache = new XcacheCache();
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return $cache;
        });
    }
}
