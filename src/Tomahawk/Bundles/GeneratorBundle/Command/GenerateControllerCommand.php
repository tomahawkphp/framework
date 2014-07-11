<?php

namespace Tomahawk\Bundles\FrameworkBundle\Command;

use Tomahawk\HttpKernel\KernelInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Tomahawk\DI\ContainerAwareInterface;
use Tomahawk\DI\ContainerInterface;

class GenerateControllerCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected $resourcesDirectory;

    protected function configure()
    {
        $this->setName('generate:controller')
            ->setDescription('Generate Controller.')
            ->addArgument(
                'controller',
                InputArgument::REQUIRED,
                'Name of controller to create'
            );

        $this->resourcesDirectory = __DIR__ . '/resources/';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $controller = $input->getArgument('controller');

        $controllerTemplate = $this->getControllerTemplate();

        if ($this->createController($controllerTemplate, $controller))
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
    protected function getControllerTemplate()
    {
        if (file_exists($this->resourcesDirectory . 'controller.txt'))
        {
            return file_get_contents($this->resourcesDirectory . 'controller.txt');
        }
    }

    /**
     * @param $controllerTemplate
     * @param $name
     * @return mixed
     */
    protected function createController($controllerTemplate, $name)
    {
        $controllerTemplate = str_replace('%name%', $name, $controllerTemplate);

        $directory = $this->getAppKernel()->getRootDir();

        if (file_put_contents($directory . '/resources/'.$name . '.php', $controllerTemplate))
        {
            return true;
        }

        return false;
    }

}