<?php

use Tomahawk\DI\DIContainer;

class DIContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DIContainer
     */
    protected $container;

    public function setUp()
    {
        $this->container = new DIContainer();
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
        $this->container->register('PersonInterface', new Person());

        $person = $this->container->get('PersonInterface');

        $this->assertInstanceOf('Person', $person);
    }

    public function testClassBuildable()
    {
        $this->container->register('PersonInterface', new Person());

        $this->assertTrue($this->container->registered('PersonInterface'));
        $this->assertFalse($this->container->registered('NotExistentInterface'));
    }

    public function testClassBuildableNonRegistered()
    {
        $this->container->register('Thing', new Thing());

        $person = $this->container->get('Person2');

        $this->assertInstanceOf('Person2', $person);
        $this->assertEquals('sfds', $person->thing->blah);
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