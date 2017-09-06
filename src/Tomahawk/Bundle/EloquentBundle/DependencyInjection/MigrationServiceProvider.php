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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Tomahawk\Bundle\EloquentBundle\Migrator\MigrationGenerator;
use Tomahawk\Bundle\EloquentBundle\Migrator\MigrationRepo;
use Tomahawk\Bundle\EloquentBundle\Migrator\Migrator;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\DependencyInjection\ContainerInterface;

/**
 * Class MigrationServiceProvider
 * @package Tomahawk\Bundle\EloquentBundle\DependencyInjection
 */
class MigrationServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container->set(MigrationRepo::class, function(ContainerInterface $c) {
            $database = $c->get(Manager::class);
            return new MigrationRepo($database->getDatabaseManager(), 'tomahawk_migrations');
        });

        $container->set(MigrationGenerator::class, function(ContainerInterface $c) {
            return new MigrationGenerator(new Filesystem());
        });

        $container->set(Migrator::class, function(ContainerInterface $c) {
            return new Migrator($c[MigrationRepo::class], new Finder(), $c->get('kernel'));
        });
    }

}
