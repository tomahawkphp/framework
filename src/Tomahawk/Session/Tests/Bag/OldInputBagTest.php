<?php

namespace Tomahawk\Routing\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Session\Bag\OldInputBag;

class OldInputBagTest extends TestCase
{
    /**
     * @var OldInputBag
     */
    private $bag;

    /**
     * @var array
     */
    protected $array = [];

    protected function setUp()
    {
        parent::setUp();
        $this->bag = new OldInputBag();
        $this->array = ['comment' => 'Old input from request'];
        $this->bag->initialize($this->array);
    }

    protected function tearDown()
    {
        $this->bag = null;
        parent::tearDown();
    }

    public function testInitialize()
    {
        $bag = new OldInputBag();
        $bag->initialize($this->array);
        $this->assertEquals($this->array, $bag->peekAll());
        $array = ['name' => 'Tom'];
        $bag->initialize($array);
        $this->assertEquals($array, $bag->peekAll());
    }

    public function testGetStorageKey()
    {
        $this->assertEquals('_th_old_input', $this->bag->getStorageKey());
        $bag = new OldInputBag('test');
        $this->assertEquals('test', $bag->getStorageKey());
    }

    public function testSetGet()
    {
        $this->bag->set('name', 'tom');

        $this->assertEquals('tom', $this->bag->get('name'));
        $this->assertNull($this->bag->get('name'));
    }

    public function testHas()
    {
        $this->assertTrue($this->bag->has('comment'));
        $this->assertFalse($this->bag->has('foo'));
    }

    public function testGetName()
    {
        $this->assertEquals('__old', $this->bag->getName());
    }

    public function testPeek()
    {
        $this->assertEquals($this->array['comment'], $this->bag->peek('comment'));
        $this->assertNull($this->bag->peek('doesnt_exist'));
    }

    public function testPeekAll()
    {
        $this->assertEquals($this->array, $this->bag->peekAll());
    }

    public function testAll()
    {
        $array = ['age' => 29];
        $bag = new OldInputBag();
        $bag->initialize($array);

        $this->assertEquals($array, $bag->all());
        $this->assertEmpty($bag->all());
    }

    public function testSetAll()
    {
        $array = ['name' => 'Tom', 'age' => 29];
        $bag = new OldInputBag();
        $bag->initialize($array);

        $this->assertEquals($array, $bag->all());
        $this->assertEmpty($bag->all());
    }
}
