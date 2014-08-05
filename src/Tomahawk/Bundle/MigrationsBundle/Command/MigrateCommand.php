<?php

namespace Tomahawk\Bundle\MigrationsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Tomahawk\Bundle\MigrationsBundle\Migration\Migrator;

class MigrateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('migration:migrate')
            ->setDescription('Run migrations.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $migrator = $this->getMigrator();

        $migrator->run();

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