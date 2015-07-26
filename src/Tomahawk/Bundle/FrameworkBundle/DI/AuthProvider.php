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

use Tomahawk\DI\ServiceProviderInterface;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\Auth\Auth;
use Tomahawk\Auth\Handlers\DatabaseAuthHandler;
use Tomahawk\Auth\Handlers\EloquentAuthHandler;
use Tomahawk\Config\ConfigInterface;

class AuthProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container->set('auth_handler', function(ContainerInterface $c) {
            $handler = $c['config']->get('security.handler');
            return $c[$handler . '_auth_handler'];
        });

        $container->set('eloquent_auth_handler', function(ContainerInterface $c) {
            $eloquentConfig = $c['config']->get('security.handlers.eloquent');
            return new EloquentAuthHandler($c['hasher'], $eloquentConfig['model']);
        });

        $container->set('database_auth_handler', function(ContainerInterface $c) {
            /** @var ConfigInterface $config */
            $config = $c['config'];

            $databaseConfig = $config->get('security.handlers.database');

            $connection = $c['illuminate_database']->getDatabaseManager()->connection($databaseConfig['connection']);

            return new DatabaseAuthHandler(
                $c['hasher'],
                $connection,
                $databaseConfig['table'],
                $databaseConfig['key'],
                $databaseConfig['password']
            );
        });

        $container->set('Tomahawk\Auth\AuthInterface', function(ContainerInterface $c) {
            return new Auth($c['session'], $c['auth_handler']);
        });

        $container->addAlias('auth', 'Tomahawk\Auth\AuthInterface');
        $container->addAlias('asset_manager', 'Tomahawk\Asset\AssetManagerInterface');
    }
}
