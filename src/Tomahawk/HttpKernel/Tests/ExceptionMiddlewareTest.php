<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\HttpKernel\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DI\Container;
use Tomahawk\HttpKernel\Middleware\ExceptionMiddleware;

class ExceptionMiddlewareTest extends TestCase
{
    public function testMiddleware()
    {
        $middleware = new ExceptionMiddleware();
        $middleware->setContainer($this->getContainer());
        $middleware->boot();
    }

    protected function getContainer()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');

        $eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $eventDispatcher
            ->expects($this->once())
            ->method('addSubscriber');

        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->setMethods(array('getEnvironment'))
            ->setConstructorArgs(array('test', false))
            ->getMockForAbstractClass();

        $kernel->expects($this->any())
            ->method('getEnvironment')
            ->will($this->returnValue('prod'));

        $templating = $this->getMock('Symfony\Component\Templating\EngineInterface');

        $templating->expects($this->any())
            ->method('render')
            ->will($this->returnValueMap(array(
                '::Error:404'
            )));

        $container = $this->getMock('Tomahawk\DI\ContainerInterface');

        $container->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('event_dispatcher', $eventDispatcher),
                array('templating', $templating),
                array('logger', $logger),
                array('kernel', $kernel),
            )));

        return $container;
    }
}
