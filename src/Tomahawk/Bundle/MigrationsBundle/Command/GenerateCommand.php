<?php

namespace Tomahawk\Bundle\MigrationsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Tomahawk\Bundle\MigrationsBundle\Migration\MigrationGenerator;
use Tomahawk\DI\ContainerAwareInterface;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\HttpKernel\Test\Kernel;

class GenerateCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface|null
     */
    private $container;

    protected function configure()
    {
        $this->setName('migration:generate')
            ->setDescription('Generate a blank migration class.')
            ->addArgument('bundle', InputArgument::REQUIRED, 'Name of bundle')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of migration');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $migrationGenerator = $this->getGenerator();

        $bundleName = $input->getArgument('bundle');
        $name = $input->getArgument('name');
        $version = date('YmdHis');
        $migrationName = sprintf('M%s%dMigration', $version, $name);

        $bundle = $this->getKernel()->getBundle($bundleName);

        $migrationGenerator->setSkeletonDirs(__DIR__ .'/../Resources');

        try
        {
            $this->getGenerator()->generate($bundle, $migrationName);
        }
        catch(IOException $e)
        {
            $output->writeln(sprintf('Error writing to "<info>%s</info>"', $bundle->getPath() .'/Migration'));
        }
        catch(\RuntimeException $e)
        {
            $output->writeln($e->getMessage());
        }

        //$output->writeln(sprintf('Generated new migration class to "<info>%s</info>"', $path));

    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return Kernel
     */
    public function getKernel()
    {
        return $this->container->get('kernel');
    }

    /**
     * @return MigrationGenerator
     */
    public function getGenerator()
    {
        return $this->container->get('migration_generator');
    }
}