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
use Tomahawk\Bundle\GeneratorBundle\Generator\ControllerGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateControllerCommand extends GenerateCommand
{

    protected $resourcesDirectory;

    protected function configure()
    {
        $this->setName('generate:controller')
            ->setDescription('Generate Controller.')
            ->addArgument('bundle', InputArgument::REQUIRED, 'Name of bundle')
            ->addArgument('controller', InputArgument::REQUIRED, 'Name of controller to create')
            ->addOption('actions', '', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The actions in the controller');

        $this->resourcesDirectory = __DIR__ . '/resources/';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $controller = $input->getArgument('controller');
        $bundleName = $input->getArgument('bundle');

        $bundle = $this->getKernel()->getBundle($bundleName);

        $generator = $this->getGenerator();

        $actions = $input->getOption('actions');

        $actions = $this->parseActions($actions);

        $generator->generate($bundle, $controller, $actions);

        $output->writeln(sprintf('Generated new controller class to "<info>%s</info>"', $bundle->getPath() . '/Controller/' . $controller . '.php'));

    }

    /**
     * @return ControllerGenerator
     */
    public function getGenerator()
    {
        return $this->container->get('controller_generator');
    }

    public function getPlaceholdersFromRoute($route)
    {
        preg_match_all('/{(.*?)}/', $route, $placeholders);
        $placeholders = $placeholders[1];

        return $placeholders;
    }

    public function parseActions($actions)
    {
        $newActions = array();

        foreach ($actions as $l => $action) {

            $data = explode(':', $action);

            // name
            if (!isset($data[0])) {
                throw new \InvalidArgumentException('An action must have a name');
            }
            $name = array_shift($data);

            // route
            $route = (isset($data[0]) && '' != $data[0]) ? array_shift($data) : '/'.substr($name, 0, -6);
            if ($route) {
                $placeholders = $this->getPlaceholdersFromRoute($route);
            } else {
                $placeholders = array();
            }

            $newActions[$name] = array(
                'name'         => $name,
                'route'        => $route,
                'placeholders' => $placeholders,
            );
        }

        return $newActions;
    }
}