<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Generator\Command;

use Symfony\Component\Console\Input\InputOption;
use Tomahawk\Generator\ModelGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateModelCommand extends GenerateCommand
{
    /**
     * @var string
     */
    protected $resourcesDirectory;

    protected function configure()
    {
        $this->setName('generate:model')
            ->setDescription('Generate Model.')
            ->addArgument('model', InputArgument::REQUIRED, 'Name of model to create')
            ->addOption('dir', 'd', InputOption::VALUE_OPTIONAL, 'Model directory',  'App')
            ->addOption('namespace', 'ns', InputOption::VALUE_OPTIONAL, 'Class Namespace',  'App')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates a new model for a given bundle.

<info>php app/hatchet %command.name% User</info>
EOT
        );

        $this->resourcesDirectory = __DIR__ . '/resources/';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $modelGenerator = $this->getGenerator();

        $model = $input->getArgument('model');
        $directory = $input->getOption('dir');
        $namespace = $input->getOption('namespace');

        $modelGenerator->generate($directory, $namespace, $model);

        $io->writeln(sprintf('Generated new model class to "<info>%s</info>"', $directory . '/'.$model . '.php'));

    }

    /**
     * @return ModelGenerator
     */
    public function getGenerator()
    {
        return $this->container->get('model_generator');
    }

}
