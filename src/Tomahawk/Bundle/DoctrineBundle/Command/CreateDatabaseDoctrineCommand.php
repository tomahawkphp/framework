<?php

/*
 * This file is part of the Doctrine Bundle
 *
 * The code was originally distributed inside the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Doctrine Project, Benjamin Eberlei <kontakt@beberlei.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\DoctrineBundle\Command;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\DriverManager;

/**
 * Database tool allows you to easily drop and create your configured databases.
 *
 * Based on the CreateDatabaseDoctrineCommand from the Symfony DoctrineBundle
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 */
class CreateDatabaseDoctrineCommand extends DoctrineCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('doctrine:database:create')
            ->setDescription('Creates the configured databases')
            ->addOption('shard', null, InputOption::VALUE_REQUIRED, 'The shard connection to use for this command')
            ->addOption('connection', null, InputOption::VALUE_OPTIONAL, 'The connection to use for this command')
            ->addOption('if-not-exists', null, InputOption::VALUE_NONE, 'Don\'t trigger an error, when the database already exists')
            ->setHelp(<<<EOT
The <info>doctrine:database:create</info> command creates the default
connections database:

<info>php app/hatchet doctrine:database:create</info>

You can also optionally specify the name of a connection to create the
database for:

<info>php app/hatchet doctrine:database:create --connection=default</info>
EOT
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connectionName = $input->getOption('connection');
        if (empty($connectionName) === true) {
            $connectionName = $this->container->get('doctrine')->getDefaultConnectionName();
        }
        $connection = $this->getDoctrineConnection($connectionName);
        $ifNotExists = $input->getOption('if-not-exists');
        $params = $connection->getParams();
        if (isset($params['master'])) {
            $params = $params['master'];
        }
        // Cannot inject `shard` option in parent::getDoctrineConnection
        // cause it will try to connect to a non-existing database
        if (isset($params['shards'])) {
            $shards = $params['shards'];
            // Default select global
            $params = array_merge($params, $params['global']);
            unset($params['global']['dbname']);
            if ($input->getOption('shard')) {
                foreach ($shards as $i => $shard) {
                    if ($shard['id'] === (int)$input->getOption('shard')) {
                        // Select sharded database
                        $params = array_merge($params, $shard);
                        unset($params['shards'][$i]['dbname'], $params['id']);
                        break;
                    }
                }
            }
        }
        $hasPath = isset($params['path']);
        $name = $hasPath ? $params['path'] : (isset($params['dbname']) ? $params['dbname'] : false);
        if (!$name) {
            throw new \InvalidArgumentException("Connection does not contain a 'path' or 'dbname' parameter and cannot be dropped.");
        }
        // Need to get rid of _every_ occurrence of dbname from connection configuration and we have already extracted all relevant info from url
        unset($params['dbname'], $params['path'], $params['url']);
        $tmpConnection = DriverManager::getConnection($params);
        $shouldNotCreateDatabase = $ifNotExists && in_array($name, $tmpConnection->getSchemaManager()->listDatabases());
        // Only quote if we don't have a path
        if (!$hasPath) {
            $name = $tmpConnection->getDatabasePlatform()->quoteSingleIdentifier($name);
        }
        $error = false;
        try {
            if ($shouldNotCreateDatabase) {
                $output->writeln(sprintf('<info>Database <comment>%s</comment> for connection named <comment>%s</comment> already exists. Skipped.</info>', $name, $connectionName));
            } else {
                $tmpConnection->getSchemaManager()->createDatabase($name);
                $output->writeln(sprintf('<info>Created database <comment>%s</comment> for connection named <comment>%s</comment></info>', $name, $connectionName));
            }
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Could not create database <comment>%s</comment> for connection named <comment>%s</comment></error>', $name, $connectionName));
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            $error = true;
        }
        $tmpConnection->close();
        return $error ? 1 : 0;
    }
}
