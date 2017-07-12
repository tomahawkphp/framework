<?php

namespace Tomahawk\Authentication\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\Authentication\AuthenticationProvider;

class AuthenticationProviderTest extends TestCase
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

        $authenticationProvider = new AuthenticationProvider(
            $userProvider,
            $passwordEncoder,
            $storage
        );

        $authenticationProvider->authorize($this->getCredentials());

        $this->assertInstanceOf('Tomahawk\Authentication\User\UserInterface', $authenticationProvider->getUser());
    }

    /**
     * @expectedException \Tomahawk\Authentication\Exception\UserNotFoundException
     * @expectedExceptionMessage User "fred" not found
     */
    public function testAuthorizeInvalidUser()
    {
        $userProvider = $this->getUserProvider();

        $userProvider->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null));

        $authenticationProvider = new AuthenticationProvider(
            $userProvider,
            $this->getPasswordEncoder(),
            $this->getStorage()
        );

        $authenticationProvider->authorize($this->getCredentials('fred'));
    }

    /**
     * @expectedException \Tomahawk\Authentication\Exception\BadCredentialsException
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

        $authenticationProvider = new AuthenticationProvider(
            $userProvider,
            $passwordEncoder,
            $this->getStorage()
        );

        $authenticationProvider->authorize($this->getCredentials('fred'));
    }

    public function testIsLoggedIn()
    {
        $storage = $this->getStorage();

        $user = $this->getUser();

        $storage->expects($this->once())
            ->method('getIdentifier')
            ->will($this->returnValue('tommy'));

        $userProvider = $this->getUserProvider();

        $userProvider->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue($user));

        $authenticationProvider = new AuthenticationProvider(
            $userProvider,
            $this->getPasswordEncoder(),
            $storage
        );

        $this->assertTrue($authenticationProvider->isLoggedIn());
    }

    public function testIsLoggedInUserCantBeLoaded()
    {
        $storage = $this->getStorage();

        $user = null;

        $storage->expects($this->once())
            ->method('getIdentifier')
            ->will($this->returnValue('tommy'));

        $userProvider = $this->getUserProvider();

        $userProvider->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue($user));

        $authenticationProvider = new AuthenticationProvider(
            $userProvider,
            $this->getPasswordEncoder(),
            $storage
        );

        $this->assertFalse($authenticationProvider->isLoggedIn());
        $this->assertTrue($authenticationProvider->isGuest());
    }

    public function testIsGuestReturnsTrueOnNoUser()
    {
        $storage = $this->getStorage();

        $storage->expects($this->once())
            ->method('getIdentifier')
            ->will($this->returnValue(null));

        $authenticationProvider = new AuthenticationProvider(
            $this->getUserProvider(),
            $this->getPasswordEncoder(),
            $storage
        );

        $this->assertTrue($authenticationProvider->isGuest());
    }

    public function testIsGuestReturnsFalseOnUser()
    {
        $storage = $this->getStorage();

        $user = $this->getUser();

        $storage->expects($this->once())
            ->method('getIdentifier')
            ->will($this->returnValue('tommy'));

        $userProvider = $this->getUserProvider();

        $userProvider->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue($user));

        $authenticationProvider = new AuthenticationProvider(
            $userProvider,
            $this->getPasswordEncoder(),
            $storage
        );

        $this->assertFalse($authenticationProvider->isGuest());
    }

    /**
     * @expectedException \Tomahawk\Authentication\Exception\UserNotFoundException
     */
    public function testLoginInvalidUser()
    {
        $user = $this->getUser();

        $user->expects($this->exactly(2))
            ->method('getUsername')
            ->will($this->returnValue('tommy'));

        $authenticationProvider = new AuthenticationProvider(
            $this->getUserProvider(),
            $this->getPasswordEncoder(),
            $this->getStorage()
        );

        $authenticationProvider->login($user);
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

        $authenticationProvider = new AuthenticationProvider(
            $userProvider,
            $this->getPasswordEncoder(),
            $storage
        );

        $authenticationProvider->login($user);

        $this->assertInstanceOf('Tomahawk\Authentication\User\UserInterface', $authenticationProvider->getUser());

        $authenticationProvider->logout();

        $this->assertNull($authenticationProvider->getUser());
    }

    private function getStorage()
    {
        return $this->getMock('Tomahawk\Authentication\Storage\StorageInterface');
    }

    private function getUser()
    {
        return $this->getMock('Tomahawk\Authentication\User\UserInterface');
    }

    private function getUserProvider()
    {
        return $this->getMock('Tomahawk\Authentication\User\UserProviderInterface');
    }

    private function getPasswordEncoder()
    {
        return $this->getMock('Tomahawk\Authentication\Encoder\PasswordEncoderInterface');
    }

    private function getCredentials($username = 'tommy', $password = 'mypassword')
    {
        $mock = $this->getMock('Tomahawk\Authentication\User\CredentialsInterface');

        $mock->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue($username));

        $mock->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($password));

        return $mock;
    }
}
