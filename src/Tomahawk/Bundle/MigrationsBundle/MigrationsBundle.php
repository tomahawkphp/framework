<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\MigrationsBundle;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Tomahawk\Bundle\MigrationsBundle\Migrator\MigrationGenerator;
use Tomahawk\Bundle\MigrationsBundle\Migrator\MigrationRepo;
use Tomahawk\Bundle\MigrationsBundle\Migrator\Migrator;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\HttpKernel\Bundle\Bundle;

class MigrationsBundle extends Bundle
{
    public function boot()
    {
        $this->container->set('migration_repo', function(ContainerInterface $c) {

            $database = $c->get('illuminate_database');
            return new MigrationRepo($database->getDatabaseManager(), 'tomahawk_migrations');
        });


        $this->container->set('migration_generator', function(ContainerInterface $c) {
            return new MigrationGenerator(new Filesystem());
        });

        $this->container->set('migrator', function(ContainerInterface $c) {
            return new Migrator($c['migration_repo'], new Finder(), $c->get('kernel'));
        });
    }
}
