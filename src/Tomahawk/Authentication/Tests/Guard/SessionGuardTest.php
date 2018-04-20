<?php

namespace Tomahawk\Authentication\Tests\Guard;

use PHPUnit\Framework\TestCase;
use Tomahawk\Authentication\Encoder\PasswordEncoderInterface;
use Tomahawk\Authentication\Guard\SessionGuard;
use Tomahawk\Authentication\User\Credentials;
use Tomahawk\Authentication\User\UserInterface;
use Tomahawk\Authentication\User\UserProviderInterface;
use Tomahawk\Session\SessionInterface;

class SessionGuardTest extends TestCase
{
    /**
     * @expectedException \Tomahawk\Authentication\Exception\UserNotFoundException
     * @throws \ReflectionException
     */
    public function testAuthorizeThrowsExceptionOnUser()
    {
        $userProvider = $this->createMock(UserProviderInterface::class);

        $userProvider->expects($this->once())
            ->method('findUserByUsername')
            ->willReturn(null)
        ;


        $session = $this->createMock(SessionInterface::class);
        $passwordEncoder = $this->createMock(PasswordEncoderInterface::class);

        $sessionGuard = new SessionGuard('default', $session, $userProvider, $passwordEncoder);
        $sessionGuard->authorize(new Credentials('username', 'password'));
    }

    /**
     * @expectedException \Tomahawk\Authentication\Exception\BadCredentialsException
     * @throws \ReflectionException
     */
    public function testAuthorizeThrowsExceptionOnPasswordCheck()
    {
        $user = $this->createMock(UserInterface::class);

        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider->expects($this->once())
            ->method('findUserByUsername')
            ->willReturn($user)
        ;


        $session = $this->createMock(SessionInterface::class);
        $passwordEncoder = $this->createMock(PasswordEncoderInterface::class);

        $sessionGuard = new SessionGuard('default', $session, $userProvider, $passwordEncoder);
        $sessionGuard->authorize(new Credentials('username', 'password'));
    }

    /**
     * @throws \ReflectionException
     */
    public function testAuthorizeReturnsUser()
    {
        $user = $this->createMock(UserInterface::class);

        $userProvider = $this->createMock(UserProviderInterface::class);

        $userProvider->expects($this->once())
            ->method('findUserByUsername')
            ->willReturn($user)
        ;


        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())
            ->method('set')
        ;

        $passwordEncoder = $this->createMock(PasswordEncoderInterface::class);
        $passwordEncoder->expects($this->once())
            ->method('isPasswordValid')
            ->willReturn(true)
        ;

        $sessionGuard = new SessionGuard('default', $session, $userProvider, $passwordEncoder);

        $user = $sessionGuard->authorize(new Credentials('username', 'password'));

        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertTrue($sessionGuard->isLoggedIn());
        $this->assertFalse($sessionGuard->isGuest());
    }

    /**
     * @expectedException \Tomahawk\Authentication\Exception\UserNotFoundException
     * @throws \ReflectionException
     */
    public function testLoginThrowsExceptionOnInvalidUser()
    {
        $user = $this->createMock(UserInterface::class);
        $user->expects($this->exactly(2))
            ->method('getUsername')
            ->willReturn('username')
        ;

        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider->expects($this->once())
            ->method('findUserByUsername')
            ->willReturn(null)
        ;

        $session = $this->createMock(SessionInterface::class);

        $passwordEncoder = $this->createMock(PasswordEncoderInterface::class);

        $sessionGuard = new SessionGuard('default', $session, $userProvider, $passwordEncoder);

        $sessionGuard->login($user);
    }

    /**
     * @throws \ReflectionException
     */
    public function testLoginOnValidUser()
    {
        $user = $this->createMock(UserInterface::class);
        $user->expects($this->exactly(2))
            ->method('getUsername')
            ->willReturn('username')
        ;

        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider->expects($this->once())
            ->method('findUserByUsername')
            ->willReturn($user)
        ;

        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())
            ->method('set')
        ;

        $passwordEncoder = $this->createMock(PasswordEncoderInterface::class);

        $sessionGuard = new SessionGuard('default', $session, $userProvider, $passwordEncoder);

        $sessionGuard->login($user);
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertTrue($sessionGuard->isLoggedIn());
        $this->assertFalse($sessionGuard->isGuest());
        $this->assertInstanceOf(UserInterface::class, $sessionGuard->getUser());

        $sessionGuard->logout();

        $this->assertFalse($sessionGuard->isLoggedIn());
        $this->assertTrue($sessionGuard->isGuest());
        $this->assertNull($sessionGuard->getUser());
    }

    /**
     * @throws \ReflectionException
     */
    public function testLoadUser()
    {
        $user = $this->createMock(UserInterface::class);

        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider->expects($this->once())
            ->method('findUserByUsername')
            ->willReturn($user)
        ;

        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())
            ->method('get')
            ->willReturn('username')
        ;

        $passwordEncoder = $this->createMock(PasswordEncoderInterface::class);

        $sessionGuard = new SessionGuard('default', $session, $userProvider, $passwordEncoder);

        $sessionGuard->loadUser();
        $this->assertTrue($sessionGuard->isLoggedIn());
        $this->assertFalse($sessionGuard->isGuest());
        $this->assertInstanceOf(UserInterface::class, $sessionGuard->getUser());
    }

    /**
     * @throws \ReflectionException
     */
    public function testIsLoginTriggerLoadUser()
    {
        $user = $this->createMock(UserInterface::class);

        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider->expects($this->once())
            ->method('findUserByUsername')
            ->willReturn($user)
        ;

        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())
            ->method('get')
            ->willReturn('username')
        ;

        $passwordEncoder = $this->createMock(PasswordEncoderInterface::class);

        $sessionGuard = new SessionGuard('default', $session, $userProvider, $passwordEncoder);

        $this->assertTrue($sessionGuard->isLoggedIn());
        $this->assertFalse($sessionGuard->isGuest());
        $this->assertInstanceOf(UserInterface::class, $sessionGuard->getUser());
    }

    /**
     * @throws \ReflectionException
     */
    public function testLoadUserOnNoUser()
    {
        $user = $this->createMock(UserInterface::class);
        $user->expects($this->never())
            ->method('getUsername')
        ;

        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider->expects($this->never())
            ->method('findUserByUsername')
        ;

        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())
            ->method('get')
            ->willReturn(null)
        ;

        $passwordEncoder = $this->createMock(PasswordEncoderInterface::class);

        $sessionGuard = new SessionGuard('default', $session, $userProvider, $passwordEncoder);

        $this->assertNull($sessionGuard->loadUser());
    }

}
