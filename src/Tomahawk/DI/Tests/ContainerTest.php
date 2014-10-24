<?php

namespace Tomahawk\DI\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DI\Container;
use Tomahawk\DI\Test\Service;

class ContainerTest extends TestCase
{
    /**
     * @var Container
     */
    protected $container;

    public function setUp()
    {
        $this->container = new Container();
        parent::setUp();
    }

    public function testConstructAddServices()
    {
        $container = new Container(array(
            'service' => new Service(),
        ));

        $this->assertTrue($container->has('service'));
    }

    public function testValue()
    {
        $this->container['foo'] = 'bar';
        $this->assertEquals($this->container['foo'], 'bar');
        $this->assertEquals($this->container->get('foo'), 'bar');
    }

    public function testClassArrayAccess()
    {
        $this->container['ServiceInterface'] = new Service();

        $service = $this->container['ServiceInterface'];

        $this->assertInstanceOf('Tomahawk\DI\Test\Service', $service);
    }


    public function testClassMake()
    {
        $this->container['ServiceInterface'] = new Service();

        $service = $this->container->get('ServiceInterface');

        $this->assertInstanceOf('Tomahawk\DI\Test\Service', $service);
    }

    public function testRemove()
    {
        $this->container['ServiceInterface'] = new Service();

        $service = $this->container->get('ServiceInterface');

        $this->assertInstanceOf('Tomahawk\DI\Test\Service', $service);

        $this->container->remove('ServiceInterface');
        $this->assertFalse($this->container->has('ServiceInterface'));
    }


    public function testClassBuildable()
    {
        $this->container['ServiceInterface'] = new Service();

        $this->assertTrue($this->container->has('ServiceInterface'));
        $this->assertFalse($this->container->has('NotExistentInterface'));
    }


    public function testClassBuildableNonRegistered()
    {
        $service = $this->container->get('Tomahawk\DI\Test\Service');

        $this->assertInstanceOf('Tomahawk\DI\Test\Service', $service);
    }


    public function testFactory()
    {
        $this->container['test'] = $this->container->factory(function(){
            return 'BOOM!';
        });

        $this->container['test2'] = $this->container->factory(function($container) {
            return $container;
        });

        $this->assertEquals('BOOM!', $this->container['test']);
        $this->assertInstanceOf('Tomahawk\DI\Container', $this->container['test2']);
    }

    /**
     * @expectedException \Tomahawk\DI\Exception\InstantiateException
     */
    public function testNonInstantiable()
    {
        $this->container->get('Tomahawk\DI\Test\AbstractService');
    }


    public function testNoConstructor()
    {
        $this->container->get('Tomahawk\DI\Test\Service2');
    }

    public function testDefaultValue()
    {
        $this->container->get('Tomahawk\DI\Test\Service3');
    }

    /**
     * @expectedException \Tomahawk\DI\Exception\BindingResolutionException
     */
    public function testNoDefaultValue()
    {
        $this->container->get('Tomahawk\DI\Test\Service4');
    }

    public function testClassDefaultValue()
    {
        $this->container->get('Tomahawk\DI\Test\Service5');
    }

    /**
     * @expectedException \Tomahawk\DI\Exception\BindingResolutionException
     */
    public function testClassNoDefaultValue()
    {
        $this->container->get('Tomahawk\DI\Test\Service6');
    }

    public function testAlias()
    {
        $this->container['ServiceInterface'] = new Service();

        $this->container->addAlias('my_service', 'ServiceInterface');

        $service = $this->container->get('my_service');

        $this->assertInstanceOf('Tomahawk\DI\Test\Service', $service);

        $service = $this->container['my_service'];

        $this->assertInstanceOf('Tomahawk\DI\Test\Service', $service);

        $this->container->removeAlias('my_service');
        $this->assertFalse($this->container->hasAlias('my_service'));
    }

    /**
     * @expectedException \RunTimeException
     */
    public function testAccessingFrozenServiceThrowsException()
    {

        $this->container['ServiceInterfaceFoo'] = function() {
            return new Service();
        };

        $this->container->addAlias('my_service_foo', 'ServiceInterfaceFoo');

        $service = $this->container['my_service_foo'];

        $this->container['ServiceInterfaceFoo'] = new Service();
    }

    public function testWhenObjectIsPassesItIsReturned()
    {
        $this->assertInstanceOf('Tomahawk\DI\Test\Service', $this->container->get(new Service()));
    }

}
