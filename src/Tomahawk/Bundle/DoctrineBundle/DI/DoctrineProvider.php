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

use Doctrine\Common\Cache\FilesystemCache;
use Tomahawk\Bundle\DoctrineBundle\Registry;
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
        $container->set('doctrine', function(ContainerInterface $c) {
            $registry = new Registry($c, array(), array('default' => $c['doctrine.entitymanager']), 'master', 'default');
            return $registry;
        });

        $container->set('doctrine.entitymanager', function(ContainerInterface $c) {

            $cache = new FilesystemCache($c['config']->get('cache.directory'));
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            $doctrineConfig = $c->get('config')->get('doctrine');

            $config = Setup::createXMLMetadataConfiguration(
                $doctrineConfig['mapping_directories'],
                $c->get('kernel')->isDebug(),
                $doctrineConfig['proxy_directories'],
                $cache
            );

            $config->setProxyNamespace($doctrineConfig['proxy_namespace']);
            //$config->setProxyDir($doctrineConfig['proxy_directories']);
            $config->setAutoGenerateProxyClasses($doctrineConfig['auto_generate_proxies']);

            return EntityManager::create($doctrineConfig['database'], $config);
        });
    }
}
