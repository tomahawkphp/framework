<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

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
        /*$container = new Container();
        $container->set('kernel', $this->getKernelMock());
        $container->set('route_listener', $this->getRouteListener());
        $frameworkBundle = new FrameworkBundle();
        $frameworkBundle->setContainer($container);
        $frameworkBundle->boot();

        $this->assertTrue($container->has('route_listener'));*/
    }

    public function getConfigMock()
    {
        $kernel = $this->getMockBuilder('Tomahawk\Config\ConfigManager')
            ->disableOriginalConstructor()
            ->getMock();

        return $kernel;
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
