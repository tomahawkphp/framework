<?php

namespace Tomahawk\Auth\Tests;

use Mockery;
use Tomahawk\Test\TestCase;
use Tomahawk\Auth\Auth;
use Tomahawk\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class AuthTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testAuthNoUser()
    {
        $sessionStorage = new MockArraySessionStorage();
        $session = new Session($sessionStorage);
        $authInterface = $this->getMock('\Tomahawk\Auth\AuthHandlerInterface');

        $user = $this->getMock('\Tomahawk\Auth\UserInterface');

        $authInterface->expects($this->any())->method('retrieveByCredentials')->willReturn($user);

        $authInterface->expects($this->any())->method('validateCredentials')->willReturn(false);

        $auth = new Auth($session, $authInterface);

        $ret = $auth->attempt(array(
            'username' => 'Tom',
            'password' => 'jdfbsfd'
        ));

        $this->assertFalse($ret);
    }

    public function testAuthUserInvalid()
    {
        $sessionStorage = new MockArraySessionStorage();
        $session = new Session($sessionStorage);
        $authInterface = $this->getMock('\Tomahawk\Auth\AuthHandlerInterface');

        $authInterface->expects($this->any())->method('retrieveByCredentials')->willReturn(null);

        $authInterface->expects($this->any())->method('validateCredentials')->willReturn(false);

        $auth = new Auth($session, $authInterface);

        $ret = $auth->attempt(array(
            'username' => 'Tom',
            'password' => 'jdfbsfd'
        ));

        $this->assertFalse($ret);
    }

    public function testAuthUserValid()
    {
        $sessionStorage = new MockArraySessionStorage();
        $session = new Session($sessionStorage);
        $authInterface = $this->getMock('\Tomahawk\Auth\AuthHandlerInterface');

        $user = $this->getMock('\Tomahawk\Auth\UserInterface');

        $user->expects($this->any())->method('getAuthIdentifier')->willReturn(1);

        $authInterface->expects($this->any())->method('retrieveByCredentials')->willReturn($user);

        $authInterface->expects($this->any())->method('validateCredentials')->willReturn(true);

        $authInterface->expects($this->any())->method('retrieveById')->willReturn(1);

        $auth = new Auth($session, $authInterface);

        $ret = $auth->attempt(array(
            'username' => 'Tom',
            'password' => 'jdfbsfd'
        ));

        $this->assertTrue($ret);

        $this->assertTrue($auth->loggedIn());
    }

    public function testGuest()
    {
        $sessionStorage = new MockArraySessionStorage();
        $session = new Session($sessionStorage);
        $authInterface = $this->getMock('\Tomahawk\Auth\AuthHandlerInterface');

        $authInterface->expects($this->any())->method('retrieveById')->willReturn(1);

        $auth = new Auth($session, $authInterface);

        $this->assertTrue($auth->isGuest());
    }

    public function testLoggedInLogout()
    {
        $sessionKey = 'login_'.md5('user');

        $sessionStorage = new MockArraySessionStorage();
        $session = new Session($sessionStorage);
        $session->set($sessionKey, 1);
        $authInterface = $this->getMock('\Tomahawk\Auth\AuthHandlerInterface');
        $authInterface->expects($this->any())->method('retrieveById')->willReturn(1);

        $auth = new Auth($session, $authInterface);

        $this->assertTrue($auth->loggedIn());

        $auth->logout();

        $this->assertTrue(!$auth->loggedIn());
    }

    public function testLogin()
    {
        $sessionStorage = new MockArraySessionStorage();
        $session = new Session($sessionStorage);
        $authInterface = $this->getMock('\Tomahawk\Auth\AuthHandlerInterface');
        $user = $this->getMock('\Tomahawk\Auth\UserInterface');
        $auth = new Auth($session, $authInterface);

        $user->expects($this->any())->method('getAuthIdentifier')->willReturn(1);
        $authInterface->expects($this->any())->method('retrieveById')->willReturn(1);
        $auth->login($user);

        $this->assertTrue($auth->loggedIn());
    }

    public function testAuthInterface()
    {
        $sessionStorage = new MockArraySessionStorage();
        $session = new Session($sessionStorage);
        $authHandlerInterface = $this->getMock('\Tomahawk\Auth\AuthHandlerInterface');
        $anotherAuthHandlerInterface = $this->getMock('\Tomahawk\Auth\AuthHandlerInterface');
        $auth = new Auth($session, $authHandlerInterface);

        $auth->setHandler($anotherAuthHandlerInterface);

        $this->assertInstanceOf('\Tomahawk\Auth\AuthHandlerInterface', $auth->getHandler());
    }

}
