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
use Tomahawk\CommandBus\CommandBus;

class CommandBusTest extends TestCase
{

    public function testCommandBusGetsHandler()
    {
        $commandHandler = $this->getCommandHandler();

        $commandHandler->expects($this->once())
            ->method('handle');

        $resolver = $this->getResolver();

        $resolver->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue($commandHandler));

        $command = $this->getCommand();

        $commandBus = new CommandBus($resolver);

        $commandBus->handle($command);
    }

    /**
     * @expectedException \Tomahawk\CommandBus\Exception\HandlerNotFoundException
     */
    public function testCommandBusThrowsException()
    {
        $resolver = $this->getResolver();

        $resolver->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue(null));

        $command = $this->getCommand();

        $commandBus = new CommandBus($resolver);

        $commandBus->handle($command);
    }

    protected function getResolver()
    {
        $resolver = $this->getMock('Tomahawk\CommandBus\CommandHandlerResolverInterface');

        return $resolver;
    }

    protected function getCommand()
    {
        $command = $this->getMock('Tomahawk\CommandBus\CommandInterface');
        return $command;
    }

    protected function getCommandHandler()
    {
        $commandHandler = $this->getMockBuilder('Tomahawk\CommandBus\CommandHandlerInterface')
            ->getMock();

        return $commandHandler;
    }
}

