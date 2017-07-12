<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests\EventListener;

use Tomahawk\DependencyInjection\Container;
use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\Bundle\FrameworkBundle\EventListener\StringResponseListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class StringResponseListenerTest extends TestCase
{

    public function testListener()
    {
        $container = $this->getContainer();

        $container->get('event_dispatcher')->addSubscriber(new StringResponseListener());

        $controllerResult = 'Hello world';
        $event = new GetResponseForControllerResultEvent($this->getKernel(), new Request(), HttpKernelInterface::MASTER_REQUEST, $controllerResult);

        $container->get('event_dispatcher')->dispatch(KernelEvents::VIEW, $event);

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
