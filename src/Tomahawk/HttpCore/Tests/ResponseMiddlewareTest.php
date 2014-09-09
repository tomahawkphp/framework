<?php

namespace Tomahawk\HttpCore\Tests;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Tomahawk\DI\Container;
use Tomahawk\Test\TestCase;
use Tomahawk\HttpCore\Middleware\Response as ResponseMiddleware;
use Tomahawk\HttpCore\Response\Cookies;

class ResponseMiddlewareTest extends TestCase
{

    public function testMiddlewareName()
    {
        $middleware = new ResponseMiddleware();
        $middleware->setContainer($this->getContainer());

        $this->assertEquals('Response', $middleware->getName());

        // Check again
        $this->assertEquals('Response', $middleware->getName());
    }

    public function testGetEventDispatcher()
    {
        $middleware = new ResponseMiddleware();
        $middleware->setContainer($this->getContainer());

        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventDispatcher', $middleware->getEventDispatcher());
    }

    public function testGetCookies()
    {
        $middleware = new ResponseMiddleware();
        $middleware->setContainer($this->getContainer());

        $this->assertInstanceOf('Tomahawk\HttpCore\Response\Cookies', $middleware->getCookies());
    }

    public function testDispatchEvent()
    {
        $container = $this->getContainer();
        $middleware = new ResponseMiddleware();
        $middleware->setContainer($container);
        $middleware->boot();

        $response = new Response();
        $container->get('cookies')->set('name', 'value');
        $event = new FilterResponseEvent($this->getKernel(), new Request(), HttpKernelInterface::MASTER_REQUEST, $response);

        $middleware->getEventDispatcher()->dispatch(KernelEvents::RESPONSE, $event);

        $response = $event->getResponse();

        $this->assertCount(1, $response->headers->getCookies());

    }

    /**
     * @return Container
     */
    protected function getContainer()
    {
        $container = new Container();
        $container->set('event_dispatcher', new EventDispatcher());
        $container->set('cookies', new Cookies(new Request(), str_repeat('a', 32)));
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
