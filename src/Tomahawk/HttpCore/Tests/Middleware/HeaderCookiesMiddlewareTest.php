<?php

namespace Tomahawk\HttpCore\Tests\Middleware;

use Tomahawk\DI\Container;
use Tomahawk\Test\TestCase;
use Tomahawk\HttpCore\Middleware\HeaderCookies;
use Tomahawk\HttpCore\Response\Cookies;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class HeaderCookiesMiddlewareTest extends TestCase
{
    public function testMiddlewareName()
    {
        $middleware = new HeaderCookies();
        $middleware->setContainer($this->getContainer());

        $this->assertEquals('HeaderCookies', $middleware->getName());

        // Check again
        $this->assertEquals('HeaderCookies', $middleware->getName());
    }

    public function testGetEventDispatcher()
    {
        $middleware = new HeaderCookies();
        $middleware->setContainer($this->getContainer());

        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventDispatcher', $middleware->getEventDispatcher());
    }

    public function testGetCookies()
    {
        $middleware = new HeaderCookies();
        $middleware->setContainer($this->getContainer());

        $this->assertInstanceOf('Tomahawk\HttpCore\Response\Cookies', $middleware->getCookies());
    }

    public function testDispatchEvent()
    {
        $container = $this->getContainer();
        $middleware = new HeaderCookies();
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
