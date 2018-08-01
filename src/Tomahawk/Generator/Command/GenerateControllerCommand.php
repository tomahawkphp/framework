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
use Tomahawk\Generator\ControllerGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class GenerateControllerCommand
 *
 * @package Tomahawk\Generator\Command
 */
class GenerateControllerCommand extends GenerateCommand
{
    /**
     * @var
     */
    protected $resourcesDirectory;

    protected function configure()
    {
        $this->setName('generate:controller')
            ->setDescription('Generate Controller.')
            ->addArgument('controller', InputArgument::REQUIRED, 'Name of controller to create')
            ->addOption('dir', 'd', InputOption::VALUE_OPTIONAL, 'Model directory',  'App')
            ->addOption('namespace', 'ns', InputOption::VALUE_OPTIONAL, 'Class Namespace',  'App')
            ->addOption('actions', 'a', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The actions in the controller')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates a new controller for a given bundle.

Running:
<info>php app/hatchet %command.name% Admin</info>

Would create a controller named <info>AdminController</info>

You can also optionally specify actions for the controller

<info>php app/hatchet %command.name% Admin --actions="find view:{id}"</info>

<info>php app/hatchet %command.name% Admin --actions="find" --actions="view:{id}"</info>
EOT
        );

        $this->resourcesDirectory = __DIR__ . '/resources/';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $controller = $input->getArgument('controller');
        $directory = $input->getOption('dir');
        $namespace = $input->getOption('namespace');

        $generator = $this->getGenerator();

        $actions = $input->getOption('actions');

        $actions = $this->parseActions($actions);

        $generator->generate($directory, $namespace, $controller, $actions);

        $io->writeln(sprintf('Generated new controller class to "<info>%s</info>"', $directory . $controller . 'Controller.php'));

    }

    /**
     * @return ControllerGenerator
     */
    public function getGenerator()
    {
        return $this->container->get('controller_generator');
    }

    public function getActionParametersFromRaw($rawParameters)
    {
        preg_match_all('/{(.*?)}/', $rawParameters, $placeholders);
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

            // action parameters
            $rawParameters = (isset($data[0]) && '' != $data[0]) ? array_shift($data) : null;

            if ($rawParameters) {
                $placeholders = $this->getActionParametersFromRaw($rawParameters);
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
