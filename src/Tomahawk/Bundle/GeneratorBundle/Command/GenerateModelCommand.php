<?php

namespace Tomahawk\Bundle\FrameworkBundle\Command;

use Tomahawk\HttpKernel\KernelInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Tomahawk\DI\ContainerAwareInterface;
use Tomahawk\DI\ContainerInterface;

class GenerateModelCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected $resourcesDirectory;

    protected function configure()
    {
        $this->setName('generate:model')
            ->setDescription('Generate Model.')
            ->addArgument(
                'model',
                InputArgument::REQUIRED,
                'Name of model to create'
            );

        $this->resourcesDirectory = __DIR__ . '/resources/';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $model = $input->getArgument('model');

        $modelTemplate = $this->getModelTemplate();

        if ($this->createModel($modelTemplate, $model))
        {
            $output->write('');
        }

    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return KernelInterface
     */
    protected function getAppKernel()
    {
        return $this->container->get('kernel');
    }

    /**
     * @return string
     */
    protected function getModelTemplate()
    {
        if (file_exists($this->resourcesDirectory . 'model.txt'))
        {
            return file_get_contents($this->resourcesDirectory . 'model.txt');
        }
    }

    /**
     * @param $modelTemplate
     * @param $name
     * @return mixed
     */
    protected function createModel($modelTemplate, $name)
    {
        $modelTemplate = str_replace('%name%', $name, $modelTemplate);

        $directory = $this->getAppKernel()->getRootDir();

        if (file_put_contents($directory . '/resources/'.$name . '.php', $modelTemplate))
        {
            return true;
        }

        return false;
    }

}