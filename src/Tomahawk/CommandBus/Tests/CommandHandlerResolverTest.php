<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\CommandBus\CommandHandlerResolver;

class CommandHandlerResolverTest extends TestCase
{
    public function testCommandHandlerResolverThrowsExceptionAndReturnsNull()
    {
        $command = $this->getCommand();

        $container = $this->getContainer();
        $container->expects($this->once())
            ->method('get')
            ->willThrowException(new \Exception());

        $resolver = new CommandHandlerResolver($container);

        $this->assertNull($resolver->resolve($command));
    }

    public function testCommandHandlerResolverReturnsClass()
    {
        $commandHandler = $this->getCommandHandler();
        $command = $this->getCommand();

        $container = $this->getContainer();
        $container->expects($this->once())
            ->method('get')
            ->will($this->returnValue($commandHandler));

        $resolver = new CommandHandlerResolver($container);

        $this->assertSame($commandHandler, $resolver->resolve($command));
    }

    protected function getContainer()
    {
        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');
        return $container;
    }

    protected function getCommand()
    {
        $command = $this->getMockBuilder('Tomahawk\CommandBus\CommandInterface')
            ->getMock();

        return $command;
    }

    protected function getCommandHandler()
    {
        $commandHandler = $this->getMockBuilder('Tomahawk\CommandBus\CommandHandlerInterface')
            ->getMock();

        return $commandHandler;
    }
}

