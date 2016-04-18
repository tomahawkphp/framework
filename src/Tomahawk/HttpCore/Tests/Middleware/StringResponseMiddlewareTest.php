<?php

namespace Tomahawk\HttpCore\Tests\Middleware;

use Tomahawk\DI\Container;
use Tomahawk\Test\TestCase;
use Tomahawk\HttpCore\Middleware\StringResponse as StringResponseMiddleware;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class StringResponseMiddlewareTest extends TestCase
{
    public function testMiddlewareName()
    {
        $middleware = new StringResponseMiddleware();
        $middleware->setContainer($this->getContainer());

        $this->assertEquals('StringResponse', $middleware->getName());

        // Check again so it pulls from cache
        $this->assertEquals('StringResponse', $middleware->getName());
    }

    public function testGetEventDispatcher()
    {
        $middleware = new StringResponseMiddleware();
        $middleware->setContainer($this->getContainer());

        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventDispatcher', $middleware->getEventDispatcher());
    }

    public function testDispatchEvent()
    {
        $middleware = new StringResponseMiddleware();
        $middleware->setContainer($this->getContainer());
        $middleware->boot();

        $controllerResult = 'Hello world';
        $event = new GetResponseForControllerResultEvent($this->getKernel(), new Request(), HttpKernelInterface::MASTER_REQUEST, $controllerResult);

        $middleware->getEventDispatcher()->dispatch(KernelEvents::VIEW, $event);

        $response = $event->getResponse();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals('Hello world', $response->getContent());
    }

    /**
     * @return Container
     */
    protected function getContainer()
    {
        $container = new Container();
        $container->set('event_dispatcher', new EventDispatcher());
        return $container;
    }

    protected function getKernel()
    {
        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();
        
        return $kernel;
    }
}
