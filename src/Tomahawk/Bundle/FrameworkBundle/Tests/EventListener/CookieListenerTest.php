<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests\EventListener;

use Tomahawk\Test\TestCase;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Bundle\FrameworkBundle\EventListener\CookieListener;
use Tomahawk\HttpCore\Response\Cookies;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class CookieListenerTest extends TestCase
{
    public function testListener()
    {
        $container = $this->getContainer();

        $container->get('event_dispatcher')->addSubscriber(new CookieListener($container));

        $response = new Response();
        $container->get('cookies')->set('name', 'value');
        $event = new FilterResponseEvent($this->getKernel(), new Request(), HttpKernelInterface::MASTER_REQUEST, $response);

        $container->get('event_dispatcher')->dispatch(KernelEvents::RESPONSE, $event);

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
