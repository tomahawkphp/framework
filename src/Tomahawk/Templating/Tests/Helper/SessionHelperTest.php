<?php

namespace Tomahawk\Routing\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Templating\Helper\SessionHelper;

class SessionHelperTest extends TestCase
{
    public function testHelperReturnsCorrectName()
    {
        $helper = new SessionHelper($this->getSession());
        $this->assertEquals('session', $helper->getName());
    }

    public function testHelperCallsGetMethod()
    {
        $session = $this->getSession();

        $session->expects($this->once())
            ->method('get');

        $helper = new SessionHelper($session);
        $helper->get('user_id');
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testInvalidMethodThrowsException()
    {
        $session = $this->getSession();

        $helper = new SessionHelper($session);
        $helper->foo();
    }

    protected function getSession()
    {
        return $this->getMock('Tomahawk\Session\SessionInterface');
    }
}
