<?php

namespace Tomahawk\DependencyInjection\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\DependencyInjection\Test\ServiceProvider;

class ServiceProviderInterfaceTest extends TestCase
{
    public function testProvider()
    {
        $container = new Container();

        $serviceProvider = new ServiceProvider();
        $serviceProvider->register($container);

        $this->assertEquals('value', $container['param']);
        $this->assertInstanceOf('Tomahawk\DependencyInjection\Test\Service', $container['service']);

        $serviceOne = $container['factory'];
        $this->assertInstanceOf('Tomahawk\DependencyInjection\Test\Service', $serviceOne);

        $serviceTwo = $container['factory'];
        $this->assertInstanceOf('Tomahawk\DependencyInjection\Test\Service', $serviceTwo);

        $this->assertNotSame($serviceOne, $serviceTwo);
    }

    public function testProviderWithRegisterMethod()
    {
        $container = new Container();

        $container->register(new ServiceProvider(), array(
            'anotherParameter' => 'anotherValue'
        ));

        $this->assertEquals('value', $container['param']);
        $this->assertEquals('anotherValue', $container['anotherParameter']);

        $this->assertInstanceOf('Tomahawk\DependencyInjection\Test\Service', $container['service']);

        $serviceOne = $container['factory'];
        $this->assertInstanceOf('Tomahawk\DependencyInjection\Test\Service', $serviceOne);

        $serviceTwo = $container['factory'];
        $this->assertInstanceOf('Tomahawk\DependencyInjection\Test\Service', $serviceTwo);

        $this->assertNotSame($serviceOne, $serviceTwo);

    }

}
