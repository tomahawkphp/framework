<?php

use Tomahawk\Auth\Auth;
use Tomahawk\Auth\AuthHandlerInterface;
use Tomahawk\Auth\Handlers\PdoAuthHandler;
use Tomahawk\Auth\Handlers\EloquentAuthHandler;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;
class AuthTest extends PHPUnit_Framework_TestCase
{
    public function testAuthNoUser()
    {
        $sessionStorage = new MockArraySessionStorage();
        $session = new Session($sessionStorage);
        $authInterface = Mockery::mock('\Tomahawk\Auth\AuthHandlerInterface');

        $user = Mockery::mock('\Tomahawk\Auth\UserInterface');

        $authInterface->shouldReceive('retrieveByCredentials')->andReturn($user);

        $authInterface->shouldReceive('validateCredentials')->andReturn(false);

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
        $authInterface = Mockery::mock('\Tomahawk\Auth\AuthHandlerInterface');

        $user = Mockery::mock('\Tomahawk\Auth\UserInterface');

        $authInterface->shouldReceive('retrieveByCredentials')->andReturn(null);

        $authInterface->shouldReceive('validateCredentials')->andReturn(false);

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
        $authInterface = Mockery::mock('\Tomahawk\Auth\AuthHandlerInterface');

        $user = Mockery::mock('\Tomahawk\Auth\UserInterface');

        $user->shouldReceive('getAuthIdentifier')->andReturn(1);

        $authInterface->shouldReceive('retrieveByCredentials')->andReturn($user);

        $authInterface->shouldReceive('validateCredentials')->andReturn(true);

        $authInterface->shouldReceive('retrieveById')->andReturn(1);

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
        $authInterface = Mockery::mock('\Tomahawk\Auth\AuthHandlerInterface');

        $user = Mockery::mock('\Tomahawk\Auth\UserInterface');

        $authInterface->shouldReceive('retrieveById')->andReturn(1);

        $auth = new Auth($session, $authInterface);

        $this->assertTrue($auth->isGuest());
    }

    public function testLoggedInLogout()
    {
        $sessionStorage = new MockArraySessionStorage();
        $session = new Session($sessionStorage);
        $session->set('login_d41d8cd98f00b204e9800998ecf8427e', 1);
        $authInterface = Mockery::mock('\Tomahawk\Auth\AuthHandlerInterface');
        $authInterface->shouldReceive('retrieveById')->andReturn(1);

        $auth = new Auth($session, $authInterface);

        $this->assertTrue($auth->loggedIn());

        $auth->logout();

        $this->assertTrue(!$auth->loggedIn());
    }

    public function testLogin()
    {
        $sessionStorage = new MockArraySessionStorage();
        $session = new Session($sessionStorage);
        $authInterface = Mockery::mock('\Tomahawk\Auth\AuthHandlerInterface');
        $user = Mockery::mock('\Tomahawk\Auth\UserInterface');
        $auth = new Auth($session, $authInterface);

        $user->shouldReceive('getAuthIdentifier')->andReturn(1);
        $authInterface->shouldReceive('retrieveById')->andReturn(1);
        $auth->login($user);

        $this->assertTrue($auth->loggedIn());
    }

    public function testAuthInterface()
    {
        $sessionStorage = new MockArraySessionStorage();
        $session = new Session($sessionStorage);
        $authHandlerInterface = Mockery::mock('\Tomahawk\Auth\AuthHandlerInterface');
        $anotherAuthHandlerInterface = Mockery::mock('\Tomahawk\Auth\AuthHandlerInterface');
        $user = Mockery::mock('\Tomahawk\Auth\UserInterface');
        $auth = new Auth($session, $authHandlerInterface);

        $auth->setHandler($anotherAuthHandlerInterface);


        $this->assertInstanceOf('\Tomahawk\Auth\AuthHandlerInterface', $auth->getHandler());
    }

}