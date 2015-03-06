<?php

namespace Tomahawk\Routing\Tests;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Tomahawk\DI\Container;
use Tomahawk\Test\TestCase;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Tomahawk\Session\Session;
use Tomahawk\Session\Middleware\Session as SessionMiddleware;

class SessionMiddlewareTest extends TestCase
{
    public function testGetName()
    {
        $middleware = new SessionMiddleware();
        $middleware->setContainer($this->getContainer());

        $this->assertEquals('Session', $middleware->getName());
    }

    public function testGetEventDispatcher()
    {
        $middleware = new SessionMiddleware();
        $middleware->setContainer($this->getContainer());

        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventDispatcher', $middleware->getEventDispatcher());
    }

    public function testGetSessionManager()
    {
        $middleware = new SessionMiddleware();
        $middleware->setContainer($this->getContainer());

        $this->assertInstanceOf('Tomahawk\Session\Session', $middleware->getSessionManager());
    }

    public function testDispatchEvent()
    {
        $session = $this->getSessionManager();

        $session->expects($this->once())
            ->method('clearOldInput');

        $session->expects($this->once())
            ->method('mergeNewInput');

        $session->expects($this->once())
            ->method('save');

        $container = $this->getContainer($session);
        $middleware = new SessionMiddleware();
        $middleware->setContainer($container);
        $middleware->boot();

        $event = new FinishRequestEvent($this->getKernel(), new Request(), HttpKernelInterface::MASTER_REQUEST);

        $middleware->getEventDispatcher()->dispatch(KernelEvents::FINISH_REQUEST, $event);
    }

    /**
     * @param null $session
     * @return Container
     */
    protected function getContainer($session = null)
    {
        if (null === $session) {
            $session = $this->getSessionManager();
        }
        $container = new Container();
        $container->set('event_dispatcher', new EventDispatcher());
        $container->set('session', $session);
        return $container;
    }

    protected function getKernel()
    {
        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();

        return $kernel;
    }

    protected function getSessionManager()
    {
        $kernel = $this->getMockBuilder('Tomahawk\Session\Session')
            ->disableOriginalConstructor()
            ->getMock();

        return $kernel;
    }
}
