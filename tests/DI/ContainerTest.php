<?php

use Tomahawk\DI\Container;
use Tomahawk\DI\ServiceProviderInterface;

class ContainerTest extends PHPUnit_Framework_TestCase
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

    public function testValue()
    {
        $this->container['foo'] = 'bar';
        $this->assertEquals($this->container['foo'], 'bar');
        $this->assertEquals($this->container->get('foo'), 'bar');
    }

    public function testClassArrayAccess()
    {
        $this->container['PersonInterface'] = new Person();

        $person = $this->container['PersonInterface'];

        $this->assertInstanceOf('Person', $person);
    }

    public function testClassMake()
    {
        $this->container->set('PersonInterface', new Person());

        $person = $this->container->get('PersonInterface');

        $this->assertInstanceOf('Person', $person);
    }

    public function testClassBuildable()
    {
        $this->container->set('PersonInterface', new Person());

        $this->assertTrue($this->container->has('PersonInterface'));
        $this->assertFalse($this->container->has('NotExistentInterface'));
    }

    public function testClassBuildableNonRegistered()
    {
        $this->container->set('Thing', new Thing());

        $person = $this->container->get('Person2');

        $this->assertInstanceOf('Person2', $person);
        $this->assertEquals('sfds', $person->thing->blah);
    }

    public function testProvider()
    {
        $this->container->register(new PersonProvider());

        $this->assertTrue($this->container->has('test'));
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

    public function testNonInstantiable()
    {
        $this->setExpectedException('Tomahawk\DI\InstantiateException');
        $this->container->get('PersonStub');
    }

    public function testNoConstructor()
    {
        $this->container->get('Person3Stub');
    }

    public function testDefaultValue()
    {
        $this->container->get('Person4Stub');
    }

    public function testNoDefaultValue()
    {
        $this->setExpectedException('Tomahawk\DI\BindingResolutionException');
        $this->container->get('Person5Stub');
    }

    public function testClassDefaultValue()
    {
        $this->container->get('People2Collection');
    }

    public function testClassNoDefaultValue()
    {
        $this->setExpectedException('Tomahawk\DI\BindingResolutionException');
        $this->container->get('PeopleCollection');
    }

}

class Thing {
    public $blah = 'sfds';
}

class Person2 {

    public $thing;

    public function __construct(Thing $thing)
    {
        $this->thing = $thing;
    }
}

class Person
{
    public $name;
}

class PersonProvider implements ServiceProviderInterface
{
    public function register(\Pimple\Container $container)
    {
        $container['test'] = 'Tom';
    }
}

abstract class PersonStub
{

}

class Person3Stub {}

class Person4Stub {

    public function __construct($thing = 'test')
    {

    }
}

class Person5Stub {

    public function __construct($thing)
    {

    }
}

class PeopleCollection {

    public function __construct(Person5Stub $person)
    {

    }
}

class People2Collection {

    public function __construct(Person5Stub $person = null)
    {

    }
}