<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\ConfigServiceProvider as ConfigProvider;

class ConfigServiceProviderTest extends TestCase
{
    /**
     * @covers Tomahawk\Bundle\FrameworkBundle\DependencyInjection\ConfigServiceProvider
     */
    public function testProvider()
    {
        $container = $this->getContainer();
        $configProvider = new ConfigProvider();
        $configProvider->register($container);

        $this->assertInstanceOf('Tomahawk\Config\ConfigInterface', $container->get('config'));
    }

    protected function getContainer()
    {
        $container = new Container();
        $container->set('request_stack', $this->getMock('Symfony\Component\HttpFoundation\RequestStack'));
        $container->set('kernel', $this->getKernel());

        return $container;
    }

    protected function getKernel()
    {
        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();

        $kernel->expects($this->any())
            ->method('getRootDir')
            ->will($this->returnValue(__DIR__ .'/Resources'));

        return $kernel;
    }
}
