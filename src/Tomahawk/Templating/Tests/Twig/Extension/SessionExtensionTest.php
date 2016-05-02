<?php

namespace Tomahawk\Templating\Tests\Twig\Extension;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
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
        $this->assertCount(3, $sessionExtension->getFunctions());
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

    public function testGetFlashBagFunctionReturnsCorrectValue()
    {
        $sessionExtension = new SessionExtension($this->getSessionMock());
        $this->assertInstanceOf(FlashBagInterface::class, $sessionExtension->getFlashBag());
    }

    protected function getSessionMock()
    {
        $session = $this->getMock('Tomahawk\Session\SessionInterface');
        $flashBag = $this->getMock(FlashBagInterface::class);

        $session->expects($this->any())
            ->method('get')
            ->will($this->returnValue('Tom'));

        $session->expects($this->any())
            ->method('has')
            ->will($this->returnValue(true));

        $session->expects($this->any())
            ->method('getFlashBag')
            ->will($this->returnValue($flashBag));

        $session->expects($this->any())
            ->method('hasFlash')
            ->will($this->returnValue(true));

        return $session;
    }
}
