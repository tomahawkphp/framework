<?php

use Tomahawk\Auth\Auth;
use Tomahawk\Auth\AuthHandlerInterface;
use Tomahawk\Auth\Handlers\EloquentAuthHandler;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;

class AuthEloquentAuthHandlerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testRetrieveByIDReturnsUser()
    {
        $provider = $this->getProviderMock();
        $mock = Mockery::mock('stdClass');
        $mock->shouldReceive('newQuery')->once()->andReturn($mock);
        $mock->shouldReceive('find')->once()->with(1)->andReturn('bar');
        $provider->expects($this->once())->method('createModel')->will($this->returnValue($mock));
        $user = $provider->retrieveByID(1);

        $this->assertEquals('bar', $user);
    }

    public function testRetrieveByCredentials()
    {
        $provider = $this->getProviderMock();
        $mock = Mockery::mock('stdClass');

        $mock->shouldReceive('newQuery')->once()->andReturn($mock);
        $mock->shouldReceive('where');
        $mock->shouldReceive('first')->once()->andReturn('bar');

        $provider->expects($this->once())->method('createModel')->will($this->returnValue($mock));

        $user = $provider->retrieveByCredentials(array(
            'username' => 'tom',
            'password' => 'password'
        ));

        $this->assertEquals('bar', $user);
    }
    public function testValidateCredentials()
    {
        $hasher = $this->getMock('Tomahawk\Hashing\HasherInterface');
        $provider = $this->getProviderMock($hasher);

        $user = $this->getMock('Tomahawk\Auth\UserInterface');
        $user->expects($this->once())->method('getAuthPassword')->willReturn('password');
        $hasher->expects($this->any())->method('check')->willReturn(true);

        $return = $provider->validateCredentials($user, array(
            'username' => 'tom',
            'password' => 'password'
        ));

        $this->assertTrue($return);

    }

    public function testCreateModelReturnCorrectInstance()
    {
        $hasher = $this->getMock('Tomahawk\Hashing\HasherInterface');

        $provider = new EloquentAuthHandler($hasher, 'UserStub');
        $model = $provider->createModel();

        $this->assertEquals('UserStub', $provider->getModelName());
        $this->assertInstanceOf('UserStub', $model);
    }

    protected function getProviderMock($hasher = null)
    {
        $hasher = $hasher ?: $this->getMock('Tomahawk\Hashing\HasherInterface');
        return $this->getMock('Tomahawk\Auth\Handlers\EloquentAuthHandler', array('createModel'), array($hasher, 'foo'));
    }

}
class UserStub {}
