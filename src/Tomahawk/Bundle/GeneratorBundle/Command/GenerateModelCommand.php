<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\GeneratorBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Tomahawk\Bundle\GeneratorBundle\Generator\ModelGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
            ->addArgument('bundle', InputArgument::REQUIRED, 'Name of bundle')
            ->addArgument('model', InputArgument::REQUIRED, 'Name of model to create')
            ->addOption('model-dir', 'md', InputOption::VALUE_OPTIONAL, 'Name of model directory to create',  'Entity')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates a new model for a given bundle.

<info>php app/hatchet %command.name% UserBundle User --model-dir="Entity"</info>
EOT
        );

        $this->resourcesDirectory = __DIR__ . '/resources/';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $modelGenerator = $this->getGenerator();

        $model = $input->getArgument('model');
        $bundleName = $input->getArgument('bundle');
        $modelDir = $input->getOption('model-dir');

        $bundle = $this->getKernel()->getBundle($bundleName);

        $modelGenerator->generate($bundle, $model, $modelDir);

        $output->writeln(sprintf('Generated new model class to "<info>%s</info>"', $bundle->getPath() . '/'.$modelDir.'/' . $model . '.php'));

    }

    /**
     * @return ModelGenerator
     */
    public function getGenerator()
    {
        return $this->container->get('model_generator');
    }

}
