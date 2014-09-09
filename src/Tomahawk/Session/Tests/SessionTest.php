<?php

namespace Symfony\Component\HttpFoundation\Session\Tests;

use Tomahawk\Test\TestCase;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Tomahawk\Session\Session;

class SessionTest extends TestCase
{
    /**
     * @var Session
     */
    protected $session;

    public function setup()
    {
        $storage = new MockArraySessionStorage();
        $this->session = new Session($storage);
    }

    public function tearDown()
    {
        $this->session = null;
    }

    public function testSessionConstructorHasSetStorage()
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface', $this->session->getStorage());
    }

    public function testHasSetGet()
    {
        $this->session->set('name', 'Tom');

        $this->assertTrue($this->session->has('name'));
        $this->assertEquals('Tom', $this->session->get('name'));
        $this->assertEquals('Fred', $this->session->get('username', 'Fred'));

        $this->session->save();
    }

    public function testDelete()
    {
        $this->session->set('name', 'Tom');
        $this->assertTrue($this->session->has('name'));

        $this->session->remove('name');
        $this->assertFalse($this->session->has('name'));
    }

    public function testHasSetGetInputOld()
    {
        $this->session->getOldBag()->set('name', 'Tom');

        $this->assertTrue($this->session->getOldBag()->has('name'));
        $this->assertEquals('Tom', $this->session->getOldBag()->get('name'));
        $this->assertEquals('Fred', $this->session->getOldBag()->get('username', 'Fred'));
    }

    public function testDeleteInputOld()
    {
        $this->session->getOldBag()->set('name', 'Tom');
        $this->assertTrue($this->session->getOldBag()->has('name'));

        $this->session->getOldBag()->remove('name');
        $this->assertFalse($this->session->getOldBag()->has('name'));

        $new_input = array(
            'place' => 'stamford'
        );

        $this->session->getOldBag()->replace($new_input);

        $this->assertCount(1, $this->session->getOldBag()->all());

        $this->assertEquals('stamford', $this->session->getOldBag()->get('place'));

        $this->session->getOldBag()->clear();
        $this->assertCount(0, $this->session->getOldBag()->all());
        $this->assertEquals(0, $this->session->getOldBag()->count());

    }

    public function testHasSetGetFlash()
    {
        $this->session->setFlash('name', 'Tom');

        $this->assertTrue($this->session->hasFlash('name'));

        $this->assertCount(1, $this->session->getFlash('name'));

        // Get flash message
        $flash = $this->session->getFlash('name');

        $this->assertCount(0, $this->session->getFlash('name'));
    }

    /**
     * @covers \Tomahawk\Session\InputOldBag::getIterator
     */
    public function testGetIterator()
    {

        $flashes = array('hello' => 'world', 'beep' => 'boop', 'notice' => 'nope');
        foreach ($flashes as $key => $val) {
            $this->session->getOldBag()->set($key, $val);
        }

        $i = 0;
        foreach ($this->session->getOldBag() as $key => $val) {
            $this->assertEquals($flashes[$key], $val);
            $i++;
        }

        $this->assertEquals(count($flashes), $i);
    }

}
