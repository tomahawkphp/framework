<?php

use Tomahawk\Input\InputManager;
use Symfony\Component\HttpFoundation\Request;
use Tomahawk\Session\SessionManager;

class InputManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SessionManager
     */
    protected $session;

    public function setUp()
    {
        $this->session = new SessionManager(array(
            'session_type' => 'array',
            'session_name' => 'tomahawk_session'
        ));

        $this->session->setOld('name', 'Tommy');

        parent::setUp();
    }

    public function testHttpGet()
    {
        $request = new Request(array('name' => 'Tom'), array());
        $input = new InputManager($request, $this->session);
        $this->assertTrue($input->getHas('name'));
        $this->assertEquals('Tom', $input->get('name'));
        $this->assertCount(1, $input->get());
    }

    public function testHttpGetOnly()
    {
        $request = new Request(array('name' => 'Tom', 'age' => 27), array());
        $input = new InputManager($request, $this->session);
        $this->assertCount(1, $input->getOnly('name'));
        $this->assertCount(2, $input->get());
    }

    public function testHttpGetExcept()
    {
        $request = new Request(array('name' => 'Tom', 'age' => 27), array());
        $input = new InputManager($request, $this->session);
        $this->assertCount(1, $input->getExcept('name'));
        $this->assertCount(2, $input->get());
    }

    public function testHttpPost()
    {
        $request = new Request(array(), array('name' => 'Tom', 'age' => 27));
        $input = new InputManager($request, $this->session);
        $this->assertTrue($input->postHas('name'));
        $this->assertEquals('Tom', $input->post('name'));
        $this->assertCount(2, $input->post());
    }

    public function testHttpPostOnly()
    {
        $request = new Request(array(), array('name' => 'Tom', 'age' => 27));
        $input = new InputManager($request, $this->session);
        $this->assertCount(1, $input->postOnly('name'));
        $this->assertCount(2, $input->post());
    }

    public function testHttpPostExcept()
    {
        $request = new Request(array(), array('name' => 'Tom', 'age' => 27));
        $input = new InputManager($request, $this->session);
        $this->assertCount(1, $input->postExcept('name'));
        $this->assertCount(2, $input->post());
    }

    public function testInputOld()
    {
        $request = new Request();
        $input = new InputManager($request, $this->session);

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
        $input = new InputManager($request, $this->session);

        $input->flash($data);
    }
}