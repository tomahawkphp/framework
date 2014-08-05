<?php

namespace Tomahawk\Input\Tests;

use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Tomahawk\Session\Session;
use Tomahawk\Test\TestCase;
use Tomahawk\Input\InputManager;
use Symfony\Component\HttpFoundation\Request;

class InputManagerTest extends TestCase
{
    public function testHttpGet()
    {
        $request = new Request(array('name' => 'Tom'), array());
        $input = new InputManager($request, $this->getSession());
        $this->assertTrue($input->getHas('name'));
        $this->assertEquals('Tom', $input->get('name'));
        $this->assertCount(1, $input->get());
    }

    public function testHttpGetOnly()
    {
        $request = new Request(array('name' => 'Tom', 'age' => 27), array());
        $input = new InputManager($request, $this->getSession());
        $this->assertCount(1, $input->getOnly('name'));
        $this->assertCount(2, $input->get());
    }

    public function testHttpGetExcept()
    {
        $request = new Request(array('name' => 'Tom', 'age' => 27), array());
        $input = new InputManager($request, $this->getSession());
        $this->assertCount(1, $input->getExcept('name'));
        $this->assertCount(2, $input->get());
    }

    public function testHttpPost()
    {
        $request = new Request(array(), array('name' => 'Tom', 'age' => 27));
        $input = new InputManager($request, $this->getSession());
        $this->assertTrue($input->postHas('name'));
        $this->assertEquals('Tom', $input->post('name'));
        $this->assertCount(2, $input->post());
    }

    public function testHttpPostOnly()
    {
        $request = new Request(array(), array('name' => 'Tom', 'age' => 27));
        $input = new InputManager($request, $this->getSession());
        $this->assertCount(1, $input->postOnly('name'));
        $this->assertCount(2, $input->post());
    }

    public function testHttpPostExcept()
    {
        $request = new Request(array(), array('name' => 'Tom', 'age' => 27));
        $input = new InputManager($request, $this->getSession());
        $this->assertCount(1, $input->postExcept('name'));
        $this->assertCount(2, $input->post());
    }

    public function testInputOld()
    {
        $request = new Request();
        $input = new InputManager($request, $this->getSession());

        $this->assertTrue($input->hasOld('name'));
        $this->assertEquals('Tommy', $input->old('name'));
        $this->assertCount(1, $input->old());
    }

    public function testFlash()
    {
        $data = array(
            'name' => 'Tom',
            'age'  => 27
        );

        $request = new Request();
        $input = new InputManager($request, $this->getSession());

        $input->flash($data);
    }
    
    protected function getSession()
    {
        $storage = new MockArraySessionStorage();
        $session = new Session($storage);

        $session->setOld('name', 'Tommy');

        return $session;
    }
}