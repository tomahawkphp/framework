<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\EloquentBundle\Command;

use Illuminate\Database\Schema\Blueprint;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tomahawk\Bundle\EloquentBundle\Migrator\MigrationRepo;
use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\DependencyInjection\ContainerInterface;

class InstallCommand extends Command implements ContainerAwareInterface
{

    protected function configure()
    {
        $this->setName('migration:install')
            ->setDescription('Install migrations table.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->getMigrationRepo();

        $blueprint = new Blueprint($repo->getTableName());

        if ($repo->createRepository($blueprint))
        {
            $output->writeln('Migration table created');
        }
        else
        {
            $output->writeln('Failed to create migration table');
        }

    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return MigrationRepo
     */
    protected function getMigrationRepo()
    {
        return $this->container->get('migration_repo');
    }

}
