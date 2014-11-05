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
        $eventDispatcher->expects($this->once())->method('addSubscriber');

        $config = $this->getConfigMock();
        $config->expects($this->at(0))->method('get')->will($this->returnValue(array(
            '127.0.0.0'
        )));

        $config->expects($this->at(1))->method('get')->will($this->returnValue(true));

        $config->expects($this->at(2))->method('get')->will($this->returnValue(array(
            'example.com'
        )));

        $container->expects($this->at(0))->method('register');
        $container->expects($this->at(1))->method('get')->will($this->returnValue($eventDispatcher));
        $container->expects($this->at(2))->method('get')->will($this->returnValue($this->getRouteListener()));
        $container->expects($this->at(3))->method('get')->will($this->returnValue($config));
        $container->expects($this->at(4))->method('get')->will($this->returnValue($config));
        $container->expects($this->at(5))->method('get')->will($this->returnValue($config));

        $frameworkBundle = new FrameworkBundle();
        $frameworkBundle->setContainer($container);
        $frameworkBundle->boot();

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
}

