<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Routing\RouteCollection;
use Tomahawk\Bundle\FrameworkBundle\Command\RoutingCommand;
use Tomahawk\Console\Application;
use Tomahawk\HttpKernel\TestKernel;
use Tomahawk\Routing\Route;
use Tomahawk\Test\TestCase;

class RoutingCommandTest extends TestCase
{
    public function testCommand()
    {
        $command = new RoutingCommand();

        $routeCollection = new RouteCollection();
        $routeCollection->add('home', new Route('/'));

        $commandTester = $this->getCommandTester($command, $routeCollection);

        $commandTester->execute(array('command' => $command->getName()));
    }

    /**
     * @param \Symfony\Component\Console\Command\Command $command
     * @param $routeCollection
     * @return CommandTester
     */
    protected function getCommandTester(Command $command, $routeCollection)
    {
        $app = new TestKernel('prod', false);
        $app->boot();
        $application = new Application($app);
        $application->setAutoExit(false);

        $container = $application->getKernel()->getContainer();

        $container->set('route_collection', $routeCollection);

        $application->add($command);

        $command = $application->find('routing:view');
        $command->setContainer($container);
        $commandTester = new CommandTester($command);

        return $commandTester;
    }
}
