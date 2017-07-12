<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\CommandBusServiceProvider as CommandBusProvider;

class CommandBusProviderTest extends TestCase
{
    /**
     * @covers \Tomahawk\Bundle\FrameworkBundle\DependencyInjection\CommandBusServiceProvider
     */
    public function testProvider()
    {
        $container = new Container();

        $commandBusProvider = new CommandBusProvider();
        $commandBusProvider->register($container);

        $this->assertInstanceOf('Tomahawk\CommandBus\CommandBus', $container->get('commandbus'));
        $this->assertInstanceOf('Tomahawk\CommandBus\CommandHandlerResolver', $container->get('commandbus_handler_resolver'));

    }

    public function getContainer()
    {
        $container = new Container();
        return $container;
    }
}
