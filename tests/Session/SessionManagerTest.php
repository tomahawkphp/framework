<?php

use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;
use Tomahawk\Session\SessionManager;

class SessionManagerTest extends PHPUnit_Framework_TestCase
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

        parent::setUp();
    }

    public function testHasSetGet()
    {
        $this->session->set('name', 'Tom');

        $this->assertTrue($this->session->has('name'));
        $this->assertEquals('Tom', $this->session->get('name'));
        $this->assertEquals('Fred', $this->session->get('username', 'Fred'));
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

}