<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\MigrationsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tomahawk\Bundle\MigrationsBundle\Migration\Migrator;

class RollbackCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('migration:rollback')
            ->setDescription('Rollback last batch of migrations.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $migrator = $this->getMigrator();

        $migrator->rollback();

        $output->writeln($migrator->getNotes());
    }

    /**
     * @return Migrator
     */
    protected function getMigrator()
    {
        return $this->container->get('migrator');
    }

}
