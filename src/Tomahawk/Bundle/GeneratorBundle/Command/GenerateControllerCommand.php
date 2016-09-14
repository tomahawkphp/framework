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
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateControllerCommand extends GenerateCommand
{

    protected $resourcesDirectory;

    protected function configure()
    {
        $this->setName('generate:controller')
            ->setDescription('Generate Controller.')
            ->addArgument('bundle', InputArgument::REQUIRED, 'Name of bundle')
            ->addArgument('controller', InputArgument::REQUIRED, 'Name of controller to create')
            ->addOption('actions', '', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The actions in the controller')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates a new controller for a given bundle.

Running:
<info>php app/hatchet %command.name% MyBundle Admin</info>

Would create a controller named <info>AdminController</info> for the bundle <info>MyBundle</info>

You can also optionally specify actions for the controller

<info>php app/hatchet %command.name% MyBundle Admin --actions="find view:{id}"</info>

<info>php app/hatchet %command.name% MyBundle Admin --actions="find" --actions="view:{id}"</info>
EOT
        );

        $this->resourcesDirectory = __DIR__ . '/resources/';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $controller = $input->getArgument('controller');
        $bundleName = $input->getArgument('bundle');

        $bundle = $this->getKernel()->getBundle($bundleName);

        $generator = $this->getGenerator();

        $actions = $input->getOption('actions');

        $actions = $this->parseActions($actions);

        $generator->generate($bundle, $controller, $actions);

        $io->writeln(sprintf('Generated new controller class to "<info>%s</info>"', $bundle->getPath() . '/Controller/' . $controller . 'Controller.php'));

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
