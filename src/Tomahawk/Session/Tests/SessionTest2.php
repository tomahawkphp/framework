<?php

namespace Tomahawk\Routing\Tests;

use Tomahawk\Common\Arr;
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

    public function testHasSetGetFlash()
    {
        $this->session->setFlash('name', 'Tom');

        $this->assertTrue($this->session->hasFlash('name'));

        $this->assertCount(1, $this->session->getFlash('name'));

        // Get flash message
        $flash = $this->session->getFlash('name');

        $this->assertCount(0, $this->session->getFlash('name'));
    }

    public function testOldInput()
    {
        $this->session->setOldInput('foo', 'bar');
        $this->assertEquals('bar', $this->session->getOldInput('foo'));
        $this->assertTrue($this->session->hasOldInput('foo'));
        $this->assertFalse($this->session->hasOldInput('baz'));
        $this->session->clearOldInput();
        $this->assertCount(0, $this->session->getOldInputBag()->all());
    }

    public function testNewInput()
    {
        $this->session->setNewInput('foo', 'bar');
        $this->assertEquals('bar', $this->session->getNewInput('foo'));
        $this->assertTrue($this->session->hasNewInput('foo'));
        $this->assertFalse($this->session->hasNewInput('baz'));
        $this->session->clearNewInput();
        $this->assertCount(0, $this->session->getNewInputBag()->all());
    }

    public function testGetInputData()
    {
        $this->session->setOldInput('name', 'Tom');
        $this->session->setNewInput('name', 'Tommy');
        $this->session->setNewInput('age', '27');

        $input = $this->session->getInputData();

        $this->assertCount(2, $this->session->getInputData());

        $this->assertEquals('Tommy', Arr::get($input, 'name'));
        $this->assertEquals('27', Arr::get($input, 'age'));
    }

    public function testReflashInput()
    {
        $this->session->setOldInput('name', 'Tom');
        $this->session->reflashInput();
        $this->assertEquals('Tom', $this->session->getNewInput('name'));
    }

    public function testMergeNewInput()
    {
        $this->session->setNewInput('name', 'Tom');
        $this->session->mergeNewInput();
        $this->assertEquals('Tom', $this->session->getOldInput('name'));
    }

}
