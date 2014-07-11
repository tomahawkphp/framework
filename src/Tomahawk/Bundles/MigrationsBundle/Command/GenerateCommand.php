<?php

namespace Migrations\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('migrations:generate')
            ->setDescription('Generate a blank migration class.')
            ->addArgument('name', InputArgument::OPTIONAL, 'Name of migration', 'Version');
            //->addOption('editor-cmd', null, InputOption::VALUE_OPTIONAL, 'Open file with this command upon creation.')
            /*->setHelp(<<<EOT
The <info>%command.name%</info> command generates a blank migration class:

    <info>%command.full_name%</info>

You can optionally specify a <comment>--editor-cmd</comment> option to open the generated file in your favorite editor:

    <info>%command.full_name% --editor-cmd=mate</info>
EOT
            );*/
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        //$configuration = $this->getMigrationConfiguration($input, $output);

        $version = date('YmdHis');
        $path = $this->generateMigration($input, $version);

        $output->writeln(sprintf('Generated new migration class to "<info>%s</info>"', $path));
    }

    protected function generateMigration(InputInterface $input, $version, $up = null, $down = null)
    {
        $name = $input->getArgument('name');

        $placeHolders = array(
            '<name>',
            '<version>',
            '<up>',
            '<down>'
        );
        $replacements = array(
            $name,
            $version,
            $up ? "        " . implode("\n        ", explode("\n", $up)) : null,
            $down ? "        " . implode("\n        ", explode("\n", $down)) : null
        );

        $dir = path('migrations');

        if ( ! file_exists($dir)) {
            throw new \InvalidArgumentException(sprintf('Migrations directory "%s" does not exist.', $dir));
        }

        $dir = rtrim($dir, '/');

        $path = $dir . '/M' . $version . $name . '.php';

        $code = str_replace($placeHolders, $replacements, static::$template);

        file_put_contents($path, $code);

        return $path;
    }
}