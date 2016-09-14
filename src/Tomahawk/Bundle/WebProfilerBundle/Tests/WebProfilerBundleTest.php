<?php

namespace Tomahawk\Bundle\WebProfilerBundle\Tests;

use Doctrine\DBAL\Logging\DebugStack;
use Tomahawk\HttpKernel\KernelInterface;
use Tomahawk\Test\TestCase;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Tomahawk\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\HttpKernel\HttpKernel;
use Tomahawk\Routing\Router;

class WebProfilerBundleTest extends TestCase
{
    protected $container;

    public function testBundle()
    {
        $httpKernel = $this->getHttpKernel();

        $eventDispatcher = $this->container['event_dispatcher'];
        $event = new FilterResponseEvent($httpKernel, new Request(), HttpKernelInterface::MASTER_REQUEST, new Response());

        $webBundle = new WebProfilerBundle();
        $webBundle->setContainer($this->container);
        $webBundle->boot();
        $webBundle->registerEvents($eventDispatcher);

        $eventDispatcher->dispatch(KernelEvents::RESPONSE, $event);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $event->getResponse());

        $webBundle->shutdown();

    }

    public function testBundleWithBodyInResponse()
    {
        $httpKernel = $this->getHttpKernel();

        $eventDispatcher = $this->container['event_dispatcher'];

        $response = new Response('<html><body></body></html>');
        $event = new FilterResponseEvent($httpKernel, new Request(), HttpKernelInterface::MASTER_REQUEST, $response);

        $webBundle = new WebProfilerBundle();
        $webBundle->setContainer($this->container);
        $webBundle->boot();
        $webBundle->registerEvents($eventDispatcher);

        $eventDispatcher->dispatch(KernelEvents::RESPONSE, $event);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $event->getResponse());

        $webBundle->shutdown();

    }

    protected function getHttpKernel()
    {
        $httpKernel = $this->getMockBuilder('Tomahawk\HttpKernel\HttpKernel')
            ->disableOriginalConstructor()
            ->getMock();

        $kernel = $this->getMockBuilder(KernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $kernel->method('getStartTime')
            ->will($this->returnValue(time()));

        $container = new Container();
        $container['event_dispatcher'] = new EventDispatcher();
        $container['http_kernel'] = $httpKernel;
        $container['kernel'] = $kernel;
        $container['config'] = $this->getConfigMock();
        $container['doctrine.query_stack'] = new DebugStack();

        $engine = $this->getMockBuilder('Symfony\Component\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $container['templating'] = $engine;

        $this->container = $container;
        return $httpKernel;
    }

    protected function getConfigMock()
    {
        $config = $this->getMock('Tomahawk\Config\ConfigInterface');

        return $config;
    }
}
