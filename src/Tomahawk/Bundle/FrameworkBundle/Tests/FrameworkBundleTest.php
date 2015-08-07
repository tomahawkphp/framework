<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Tomahawk\DI\Container;
use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\FrameworkBundle\FrameworkBundle;
use Tomahawk\HttpKernel\Kernel;

class FrameworkBundleTest extends TestCase
{
    protected $container;

    public function testBundleReturnsConfig()
    {
        $container = new Container();
        $container->set('config', $this->getConfigMock());
        $bundle = new FrameworkBundle();
        $bundle->setContainer($container);

        $this->assertInstanceOf('Tomahawk\Config\ConfigManager', $bundle->getConfig());
    }

    public function testBundle()
    {
        $container = $this->getContainerMock();

        $eventDispatcher = $this->getEventDispatcherMock();
        $eventDispatcher->expects($this->exactly(2))->method('addSubscriber');

        $config = $this->getConfigMock();

        $config->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('kernel.trusted_proxies', null, array('127.0.0.0')),
                array('kernel.http_method_override', null, true),
                array('kernel.trusted_hosts',null, array('example.com')),
            )));

        $container->expects($this->atLeast(1))->method('register');

        $container->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('event_dispatcher', $eventDispatcher),
                array('route_listener', $this->getRouteListener()),
                array('locale_listener', $this->getLocaleListener()),
                array('config', $config),
            )));

        $frameworkBundle = new FrameworkBundle();
        $frameworkBundle->setContainer($container);
        $frameworkBundle->boot();
        $frameworkBundle->registerEvents($eventDispatcher);
    }

    public function getEventDispatcherMock()
    {
        $eventDispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->getMock();

        return $eventDispatcher;
    }

    public function getContainerMock()
    {
        $container = $this->getMockBuilder('Tomahawk\DI\Container')
            ->disableOriginalConstructor()
            ->getMock();

        return $container;
    }

    public function getConfigMock()
    {
        $config = $this->getMockBuilder('Tomahawk\Config\ConfigManager')
            ->disableOriginalConstructor()
            ->getMock();

        return $config;
    }

    public function getKernelMock()
    {
        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();

        return $kernel;
    }

    public function getRouteListener()
    {
        $listener = $this->getMockBuilder('Symfony\Component\HttpKernel\EventListener\RouterListener')
            ->disableOriginalConstructor()
            ->getMock();

        return $listener;
    }

    public function getLocaleListener()
    {
        $listener = $this->getMockBuilder('Tomahawk\Bundle\FrameworkBundle\Events\LocaleListener')
            ->disableOriginalConstructor()
            ->getMock();

        return $listener;
    }
}
