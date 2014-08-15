<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\FrameworkBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Tomahawk\DI\ContainerAwareInterface;
use Tomahawk\DI\ContainerInterface;
use Symfony\Component\Routing\RouteCollection;

class RoutingCommand extends Command implements ContainerAwareInterface
{
    protected $container;

    protected function configure()
    {
        $this->setName('routing:view')->setDescription('View routes.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $routeCollection = $this->getRouteCollection();

        $routes = array();

        foreach ($routeCollection->all() as $name => $route)
        {
            $routes[] = array(
                $name,
                implode(',', $route->getMethods()),
                $route->getPath(),
            );
        }

        $table = new Table($output);

        $table
            ->setHeaders(array('Name', 'Method', 'Path'))
            ->setRows($routes);

        $table->render();

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
     * @return RouteCollection
     */
    protected function getRouteCollection()
    {
        return $this->container->get('route_collection');
    }

}