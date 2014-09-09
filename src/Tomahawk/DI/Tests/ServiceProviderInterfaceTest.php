<?php

namespace Tomahawk\DI\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DI\Container;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\DI\ServiceProviderInterface;
use Tomahawk\DI\Test\ServiceProvider;

class ServiceProviderInterfaceTest extends TestCase
{
    public function testProvider()
    {
        $container = new Container();

        $serviceProvider = new ServiceProvider();
        $serviceProvider->register($container);

        $this->assertEquals('value', $container['param']);
        $this->assertInstanceOf('Tomahawk\DI\Test\Service', $container['service']);

        $serviceOne = $container['factory'];
        $this->assertInstanceOf('Tomahawk\DI\Test\Service', $serviceOne);

        $serviceTwo = $container['factory'];
        $this->assertInstanceOf('Tomahawk\DI\Test\Service', $serviceTwo);

        $this->assertNotSame($serviceOne, $serviceTwo);
    }

    public function testProviderWithRegisterMethod()
    {
        $container = new Container();

        $container->registerProvider(new ServiceProvider(), array(
            'anotherParameter' => 'anotherValue'
        ));

        $this->assertEquals('value', $container['param']);
        $this->assertEquals('anotherValue', $container['anotherParameter']);

        $this->assertInstanceOf('Tomahawk\DI\Test\Service', $container['service']);

        $serviceOne = $container['factory'];
        $this->assertInstanceOf('Tomahawk\DI\Test\Service', $serviceOne);

        $serviceTwo = $container['factory'];
        $this->assertInstanceOf('Tomahawk\DI\Test\Service', $serviceTwo);

        $this->assertNotSame($serviceOne, $serviceTwo);

    }

}
