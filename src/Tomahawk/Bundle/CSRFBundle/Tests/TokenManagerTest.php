<?php

namespace Tomahawk\Bundle\CSRFBundle\Test;

use PHPUnit\Framework\TestCase;
use Tomahawk\Bundle\CSRFBundle\Token\TokenManager;

class TokenManagerTest extends TestCase
{
    public function testGetTokenName()
    {
        $tokenManager = $this->getTokenManager();

        $this->assertEquals('_csrf_token', $tokenManager->getTokenName());
    }

    public function testGenerateToken()
    {
        $session = $this->getSession();

        $session->expects($this->at(0))
            ->method('has')
            ->will($this->returnValue(false));

        $session->expects($this->at(1))
            ->method('has')
            ->will($this->returnValue(false));

        $session->expects($this->at(2))
            ->method('has')
            ->will($this->returnValue(true));

        $token = 'foo';

        $session->expects($this->exactly(1))
            ->method('set');

        $session->expects($this->once())
            ->method('get')
            ->will($this->returnValue($token));

        $tokenManager = $this->getTokenManager($session);

        $this->assertFalse($tokenManager->hasToken());

        $tokenManager->generateToken();

        $this->assertTrue($tokenManager->hasToken());

        $this->assertEquals($token, $tokenManager->getToken());
    }

    protected function getTokenManager($session = null)
    {
        if ( ! $session) {
            $session = $this->getSession();
        }

        return new TokenManager($session);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getSession()
    {
        $session = $this->getMockBuilder('Tomahawk\Session\SessionInterface')->getMock();
        return $session;
    }
}
