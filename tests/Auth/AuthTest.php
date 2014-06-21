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

        $auth = new Auth($session, $authInterface);

        $ret = $auth->attempt(array(
            'username' => 'Tom',
            'password' => 'jdfbsfd'
        ));

        $this->assertTrue($ret);
    }


}