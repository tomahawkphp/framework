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
        if (empty($actions) || $actions !== array_values($actions)) {
            return $actions;
        }

        $parsedActions = array();

        foreach ($actions as $l => $action) {

            $data = explode(':', $action);

            // name
            $name = array_shift($data);

            // route parameters
            $route = (isset($data[0]) && '' != $data[0]) ? array_shift($data) : null;

            if ($route) {
                $placeholders = $this->getPlaceholdersFromRoute($route);
            }
            else {
                $placeholders = array();
            }

            $parsedActions[$name] = array(
                'name'         => $name,
                'placeholders' => $placeholders,
            );
        }

        return $parsedActions;
    }
}
