<?php

namespace Tomahawk\Authentication\Tests\Guard;

use PHPUnit\Framework\TestCase;
use Tomahawk\Authentication\Encoder\PasswordEncoderInterface;
use Tomahawk\Authentication\Guard\SessionGuard;
use Tomahawk\Authentication\User\Credentials;
use Tomahawk\Authentication\User\UserInterface;
use Tomahawk\Authentication\User\UserProviderInterface;
use Tomahawk\Hashing\HasherInterface;
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
        $hasher = $this->createMock(HasherInterface::class);

        $sessionGuard = new SessionGuard('default', $session, $userProvider, $hasher);
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
        $hasher = $this->createMock(HasherInterface::class);

        $sessionGuard = new SessionGuard('default', $session, $userProvider, $hasher);
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

        $hasher = $this->createMock(HasherInterface::class);
        $hasher->expects($this->once())
            ->method('check')
            ->willReturn(true)
        ;

        $sessionGuard = new SessionGuard('default', $session, $userProvider, $hasher);

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

        $hasher = $this->createMock(HasherInterface::class);

        $sessionGuard = new SessionGuard('default', $session, $userProvider, $hasher);

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

        $hasher = $this->createMock(HasherInterface::class);

        $sessionGuard = new SessionGuard('default', $session, $userProvider, $hasher);

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

        $hasher = $this->createMock(HasherInterface::class);

        $sessionGuard = new SessionGuard('default', $session, $userProvider, $hasher);

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

        $hasher = $this->createMock(HasherInterface::class);

        $sessionGuard = new SessionGuard('default', $session, $userProvider, $hasher);

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

        $hasher = $this->createMock(HasherInterface::class);

        $sessionGuard = new SessionGuard('default', $session, $userProvider, $hasher);

        $this->assertNull($sessionGuard->loadUser());
    }

}
