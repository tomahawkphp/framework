<?php

namespace Tomahawk\Bridge\Eloquent\DependencyInjection;

use Illuminate\Database\Capsule\Manager;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\BootableProviderInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;

/**
 * Class EloquentServiceProvider
 * @package Tomahawk\Bridge\Eloquent\DependencyInjection
 */
class EloquentServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    /**
     * @param ContainerInterface $container An Container instance
     */
    public function boot(ContainerInterface $container)
    {
        // This will boot Eloquent
        $container->get(Manager::class);
    }

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
        $container->set(Manager::class, function(ContainerInterface $c) {

            /** @var ConfigInterface $config */
            $config = $c->get(ConfigInterface::class);

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

        $container->set(DatabaseMigrationRepository::class, function(ContainerInterface $c) {
            /** @var Manager $database */
            $database = $c->get(Manager::class);

            /** @var ConfigInterface $config */
            $config = $c->get(ConfigInterface::class);

            $table = $config->get('database.migration_table', 'tomahawk_migrations');

            return new DatabaseMigrationRepository($database->getDatabaseManager(), $table);
        });

        /*$container->set(MigrationGenerator::class, function(ContainerInterface $c) {
            return new MigrationGenerator(new Filesystem());
        });*/

        $container->set(Migrator::class, function(ContainerInterface $c) {
            return new Migrator($c[MigrationRepo::class], new Finder(), $c->get('kernel'));
        });
    }
}
