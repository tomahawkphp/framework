<?php

namespace Tomahawk\Templating\Tests\Twig\Extension;

use Tomahawk\Test\TestCase;
use Tomahawk\Templating\Twig\Extension\SessionExtension;

class SessionExtensionTest extends TestCase
{

    public function testExtensionNameIsReturned()
    {
        $sessionExtension = new SessionExtension($this->getSessionMock());
        $this->assertEquals('session', $sessionExtension->getName());
    }

    public function testCorrectNumberOfFunctionsAreReturned()
    {
        $sessionExtension = new SessionExtension($this->getSessionMock());
        $this->assertCount(4, $sessionExtension->getFunctions());
    }

    public function testGetFunctionReturnsCorrectValue()
    {
        $sessionExtension = new SessionExtension($this->getSessionMock());
        $this->assertEquals('Tom', $sessionExtension->get('name'));
    }

    public function testHasFunctionReturnsCorrectValue()
    {
        $sessionExtension = new SessionExtension($this->getSessionMock());
        $this->assertTrue($sessionExtension->has('name'));
    }

    public function testGetFlashFunctionReturnsCorrectValue()
    {
        $sessionExtension = new SessionExtension($this->getSessionMock());
        $this->assertEquals(26, $sessionExtension->getFlash('age'));
    }

    public function testHasFlashFunctionReturnsCorrectValue()
    {
        $sessionExtension = new SessionExtension($this->getSessionMock());
        $this->assertTrue($sessionExtension->hasFlash('age'));
    }

    protected function getSessionMock()
    {
        $session = $this->getMock('Tomahawk\Session\SessionInterface');

        $session->expects($this->any())
            ->method('get')
            ->will($this->returnValue('Tom'));

        $session->expects($this->any())
            ->method('has')
            ->will($this->returnValue(true));

        $session->expects($this->any())
            ->method('getFlash')
            ->will($this->returnValue('26'));

        $session->expects($this->any())
            ->method('hasFlash')
            ->will($this->returnValue(true));

        return $session;
    }
}
