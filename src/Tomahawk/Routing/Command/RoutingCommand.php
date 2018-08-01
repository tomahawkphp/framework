<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Routing\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class RoutingCommand
 *
 * @package Tomahawk\Routing\Command
 */
class RoutingCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this->setName('routing:view')->setDescription('View routes.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $routeCollection = $this->getRouteCollection();

        $routes = array();

        foreach ($routeCollection->all() as $name => $route) {
            $routes[] = array(
                $name,
                implode(',', $route->getMethods()),
                $route->getPath(),
            );
        }

        $io->table(array('Name', 'Method', 'Path'), $routes);
    }

    /**
     * @return RouteCollection
     */
    protected function getRouteCollection()
    {
        return $this->container->get('route_collection');
    }

}
