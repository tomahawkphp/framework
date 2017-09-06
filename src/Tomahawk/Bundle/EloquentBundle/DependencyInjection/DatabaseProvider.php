<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\EloquentBundle\DependencyInjection;

use Illuminate\Database\Capsule\Manager;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\Config\ConfigInterface;

class DatabaseProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container->set(Manager::class, function(ContainerInterface $c) {

            /** @var ConfigInterface $config */
            $config = $c['config'];

            $manager = new Manager();

            $connections = $config->get('database.connections', []);

            foreach ($connections as $name => $connection) {
                $manager->addConnection($connection, $name);
            }

            $manager->setFetchMode($config->get('database.fetch'));
            $manager->getDatabaseManager()->setDefaultConnection($config->get('database.default'));

            $manager->bootEloquent();
            $manager->setAsGlobal();

            return $manager;
        });
    }

}
