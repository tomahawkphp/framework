<?php

namespace Tomahawk\Auth\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Auth\AuthManager;

class AuthMangerTest extends TestCase
{
    public function testAuthorize()
    {
        $user = $this->getUser();

        $userProvider = $this->getUserProvider();

        $userProvider->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue($user));

        $passwordEncoder = $this->getPasswordEncoder();

        $passwordEncoder->expects($this->once())
            ->method('isPasswordValid')
            ->will($this->returnValue(true));

        $storage = $this->getStorage();

        $storage->expects($this->once())
            ->method('setIdentifier');

        $authManager = new AuthManager(
            $userProvider,
            $passwordEncoder,
            $storage
        );

        $authManager->authorize($this->getCredentials());

        $this->assertInstanceOf('Tomahawk\Auth\User\UserInterface', $authManager->getUser());
    }

    /**
     * @expectedException \Tomahawk\Auth\Exception\UserNotFoundException
     * @expectedExceptionMessage User "fred" not found
     */
    public function testAuthorizeInvalidUser()
    {
        $userProvider = $this->getUserProvider();

        $userProvider->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null));

        $authManager = new AuthManager(
            $userProvider,
            $this->getPasswordEncoder(),
            $this->getStorage()
        );

        $authManager->authorize($this->getCredentials('fred'));
    }

    /**
     * @expectedException \Tomahawk\Auth\Exception\BadCredentialsException
     * @expectedExceptionMessage Password for user "fred" was incorrect
     */
    public function testAuthorizeInvalidPassword()
    {
        $user = $this->getUser();

        $userProvider = $this->getUserProvider();

        $userProvider->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue($user));

        $passwordEncoder = $this->getPasswordEncoder();

        $passwordEncoder->expects($this->once())
            ->method('isPasswordValid')
            ->will($this->returnValue(false));

        $authManager = new AuthManager(
            $userProvider,
            $passwordEncoder,
            $this->getStorage()
        );

        $authManager->authorize($this->getCredentials('fred'));
    }

    public function testIsLoggedIn()
    {
        $storage = $this->getStorage();

        $storage->expects($this->once())
            ->method('getIdentifier')
            ->will($this->returnValue('tommy'));

        $authManager = new AuthManager(
            $this->getUserProvider(),
            $this->getPasswordEncoder(),
            $storage
        );

        $this->assertTrue($authManager->isLoggedIn());
    }

    public function testIsGuest()
    {
        $storage = $this->getStorage();

        $storage->expects($this->once())
            ->method('getIdentifier')
            ->will($this->returnValue('tommy'));

        $authManager = new AuthManager(
            $this->getUserProvider(),
            $this->getPasswordEncoder(),
            $storage
        );

        $this->assertFalse($authManager->isGuest());
    }

    /**
     * @expectedException \Tomahawk\Auth\Exception\UserNotFoundException
     */
    public function testLoginInvalidUser()
    {
        $user = $this->getUser();

        $user->expects($this->exactly(2))
            ->method('getUsername')
            ->will($this->returnValue('tommy'));

        $authManager = new AuthManager(
            $this->getUserProvider(),
            $this->getPasswordEncoder(),
            $this->getStorage()
        );

        $authManager->login($user);
    }

    public function testLoginLogout()
    {
        $user = $this->getUser();

        $user->expects($this->exactly(2))
            ->method('getUsername')
            ->will($this->returnValue('tommy'));

        $userProvider = $this->getUserProvider();

        $userProvider->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue($user));

        $storage = $this->getStorage();

        $storage->expects($this->once())
            ->method('setIdentifier');

        $storage->expects($this->once())
            ->method('removeIdentifier');

        $authManager = new AuthManager(
            $userProvider,
            $this->getPasswordEncoder(),
            $storage
        );

        $authManager->login($user);

        $this->assertInstanceOf('Tomahawk\Auth\User\UserInterface', $authManager->getUser());

        $authManager->logout();

        $this->assertNull($authManager->getUser());
    }

    private function getStorage()
    {
        return $this->getMock('Tomahawk\Auth\Storage\StorageInterface');
    }

    private function getUser()
    {
        return $this->getMock('Tomahawk\Auth\User\UserInterface');
    }

    private function getUserProvider()
    {
        return $this->getMock('Tomahawk\Auth\User\UserProviderInterface');
    }

    private function getPasswordEncoder()
    {
        return $this->getMock('Tomahawk\Auth\Encoder\PasswordEncoderInterface');
    }

    private function getCredentials($username = 'tommy', $password = 'mypassword')
    {
        $mock = $this->getMock('Tomahawk\Auth\User\CredentialsInterface');

        $mock->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue($username));

        $mock->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($password));

        return $mock;
    }
}
