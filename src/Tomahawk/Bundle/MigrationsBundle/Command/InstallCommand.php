<?php

namespace Migrations\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Migrations\MigrationRepo;

use Illuminate\Database\Capsule\Manager as DB;

class InstallCommand extends BaseCommand
{

    protected function configure()
    {
        $this
            ->setName('migrations:install')
            ->setDescription('Install migrations table.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $table = 'laravel_migrations';

        $connection = DB::schema()->getConnection();

        $repo = new MigrationRepo($connection, $table);

        // Check if table exists
        if ($repo->repositoryExists()) {
            $output->writeln('<info>already exists</info>');
            return;
        }

        $repo->createRepository();

        // If not create table
        $output->writeln('Created migration table <info>'.$table.'</info>');

    }


}