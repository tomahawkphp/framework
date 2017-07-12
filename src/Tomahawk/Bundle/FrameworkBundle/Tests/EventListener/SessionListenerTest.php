<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests\EventListener;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\Bundle\FrameworkBundle\EventListener\SessionListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class SessionListenerTest extends TestCase
{
    public function testListener()
    {
        $eventDispatcher = new EventDispatcher();

        $session = $this->getMock(SessionInterface::class);

        $container = $this->getMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('get')
            ->will($this->returnValue($session));

        $listener = new SessionListener($container);

        $eventDispatcher->addSubscriber($listener);

        $event = new GetResponseEvent($this->getKernel(), new Request(), HttpKernelInterface::MASTER_REQUEST);

        $eventDispatcher->dispatch(KernelEvents::REQUEST, $event);
    }

    protected function getKernel()
    {
        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();

        return $kernel;
    }
}
