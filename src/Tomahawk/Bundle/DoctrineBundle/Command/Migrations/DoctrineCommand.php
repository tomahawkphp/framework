<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * The code is based off the Doctrine Migrations Bundle by the Doctrine Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\DoctrineBundle\Command\Migrations;

use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\Bundle\DoctrineBundle\Command\DoctrineCommand as BaseCommand;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Configuration\AbstractFileConfiguration;

/**
 * @author Tom Ellis
 *
 * Base class for Doctrine console commands to extend from.
 *
 * @author Tom Ellis
 *
 * Based on the original by:
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class DoctrineCommand extends BaseCommand
{
    public static function configureMigrations(ContainerInterface $container, Configuration $configuration)
    {
        $config  = $container->get('config');

        $kernel = $container->get('kernel');

        $defaultPath = $kernel->getRootDir();

        $defaultMigrationsDirectory = $defaultPath . '/Resources/Doctrine/migrations';
        $defaultNamespace = 'DoctrineMigrations';
        $migrationName = 'Migration';

        if ( ! $configuration->getMigrationsDirectory()) {
            $dir = $config->get('doctrine.migrations_directory', $defaultMigrationsDirectory);

            if ( ! file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $configuration->setMigrationsDirectory($dir);
        }
        else {
            $dir = $configuration->getMigrationsDirectory();

            if ( ! file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $configuration->setMigrationsDirectory($dir);
        }

        if ( ! $configuration->getMigrationsNamespace()) {
            $configuration->setMigrationsNamespace($config->get('doctrine.migration_namespace', $defaultNamespace));
        }

        if ( ! $configuration->getName()) {
            $configuration->setName($config->get('doctrine.migration_name', $migrationName));
        }

        // Migrations is not register from configuration loader
        if ( ! ($configuration instanceof AbstractFileConfiguration)) {
            $configuration->registerMigrationsFromDirectory($configuration->getMigrationsDirectory());
        }

        self::injectContainerToMigrations($container, $configuration->getMigrations());
    }

    /**
     * Injects the container to migrations aware of it
     */
    private static function injectContainerToMigrations(ContainerInterface $container, array $versions)
    {
        foreach ($versions as $version) {
            $migration = $version->getMigration();
            if ($migration instanceof ContainerAwareInterface) {
                $migration->setContainer($container);
            }
        }
    }
}
