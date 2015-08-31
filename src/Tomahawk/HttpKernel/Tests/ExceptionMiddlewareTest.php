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
        $config = $this->getMock('Tomahawk\Config\ConfigInterface');

        $config->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('error.template_404', 'path/to/file.php'),
                array('error.template_50x', 'path/to/file.php'),
            )));

        $logger = $this->getMock('Psr\Log\LoggerInterface');

        $eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $eventDispatcher
            ->expects($this->once())
            ->method('addSubscriber');

        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();

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
                array('config', $config),
            )));

        return $container;
    }
}
