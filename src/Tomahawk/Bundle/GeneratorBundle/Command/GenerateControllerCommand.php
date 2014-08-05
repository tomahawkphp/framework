<?php

namespace Tomahawk\Bundle\GeneratorBundle\Command;

use Tomahawk\Bundle\GeneratorBundle\Generator\ControllerGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class GenerateControllerCommand extends GenerateCommand
{

    protected $resourcesDirectory;

    protected function configure()
    {
        $this->setName('generate:controller')
            ->setDescription('Generate Controller.')
            ->addArgument('bundle', InputArgument::REQUIRED, 'Name of bundle')
            ->addArgument('controller', InputArgument::REQUIRED, 'Name of controller to create');

        $this->resourcesDirectory = __DIR__ . '/resources/';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $controller = $input->getArgument('controller');
        $bundleName = $input->getArgument('bundle');

        $bundle = $this->getKernel()->getBundle($bundleName);

        $generator = $this->getGenerator();

        $generator->generate($bundle, $controller);

    }

    /**
     * @return ControllerGenerator
     */
    public function getGenerator()
    {
        return $this->container->get('controller_generator');
    }
}