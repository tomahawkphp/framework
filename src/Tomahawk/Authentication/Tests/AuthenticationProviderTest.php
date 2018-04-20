<?php

namespace Tomahawk\Authentication\Tests;

use PHPUnit\Framework\TestCase;
use Tomahawk\Authentication\AuthenticationProvider;
use Tomahawk\Authentication\Factory\GuardFactory;
use Tomahawk\Authentication\Guard\GuardInterface;
use Tomahawk\Authentication\User\Credentials;
use Tomahawk\Authentication\User\UserInterface;

class AuthenticationProviderTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testAuthorize()
    {
        $credentials = new Credentials('username', 'password');

        $user = $this->createMock(UserInterface::class);

        $guard = $this->createMock(GuardInterface::class);

        $guard->expects($this->once())
            ->method('authorize')
            ->willReturn($user)
        ;

        $guardFactory = $this->createMock(GuardFactory::class);

        $authenticationProvider = new AuthenticationProvider(
            $guardFactory,
            'user'
        );

        $guardFactory->expects($this->once())
            ->method('make')
            ->with('user')
            ->willReturn($guard)
        ;

        $authenticationProvider->authorize($credentials);
    }

    /**
     * @throws \ReflectionException
     */
    public function testIsLoggedInAndGuest()
    {
        $guard = $this->createMock(GuardInterface::class);

        $guard->expects($this->exactly(2))
            ->method('isLoggedIn')
            ->willReturn(true)
        ;

        $guardFactory = $this->createMock(GuardFactory::class);

        $authenticationProvider = new AuthenticationProvider(
            $guardFactory,
            'user'
        );

        $guardFactory->expects($this->once())
            ->method('make')
            ->with('user')
            ->willReturn($guard)
        ;

        $this->assertTrue($authenticationProvider->isLoggedIn());
        $this->assertFalse($authenticationProvider->isGuest());
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetUser()
    {
        $user = $this->createMock(UserInterface::class);

        $guard = $this->createMock(GuardInterface::class);

        $guard->expects($this->once())
            ->method('isLoggedIn')
            ->willReturn(true)
        ;

        $guard->expects($this->once())
            ->method('getUser')
            ->willReturn($user)
        ;

        $guardFactory = $this->createMock(GuardFactory::class);

        $authenticationProvider = new AuthenticationProvider(
            $guardFactory,
            'user'
        );

        $guardFactory->expects($this->once())
            ->method('make')
            ->with('user')
            ->willReturn($guard)
        ;

        $this->assertSame($user, $authenticationProvider->getUser());
    }

    /**
     * @throws \ReflectionException
     */
    public function testLoginLogout()
    {
        $user = $this->createMock(UserInterface::class);

        $guard = $this->createMock(GuardInterface::class);

        $guard->expects($this->once())
            ->method('login')
        ;

        $guard->expects($this->once())
            ->method('logout')
        ;

        $guardFactory = $this->createMock(GuardFactory::class);

        $authenticationProvider = new AuthenticationProvider(
            $guardFactory,
            'user'
        );

        $guardFactory->expects($this->once())
            ->method('make')
            ->with('user')
            ->willReturn($guard)
        ;

        $authenticationProvider->login($user);
        $authenticationProvider->logout();
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetUserReturnsNull()
    {
        $guard = $this->createMock(GuardInterface::class);

        $guard->expects($this->once())
            ->method('isLoggedIn')
            ->willReturn(false)
        ;

        $guard->expects($this->never())
            ->method('getUser')
        ;

        $guardFactory = $this->createMock(GuardFactory::class);

        $authenticationProvider = new AuthenticationProvider(
            $guardFactory,
            'user'
        );

        $guardFactory->expects($this->once())
            ->method('make')
            ->with('user')
            ->willReturn($guard)
        ;

        $this->assertNull($authenticationProvider->getUser());
    }

    /**
     * @throws \ReflectionException
     */
    public function testLoadUser()
    {
        $user = $this->createMock(UserInterface::class);

        $guard = $this->createMock(GuardInterface::class);

        $guard->expects($this->once())
            ->method('loadUser')
            ->willReturn($user)
        ;

        $guardFactory = $this->createMock(GuardFactory::class);

        $authenticationProvider = new AuthenticationProvider(
            $guardFactory,
            'user'
        );

        $guardFactory->expects($this->once())
            ->method('make')
            ->with('user')
            ->willReturn($guard)
        ;

        $this->assertSame($user, $authenticationProvider->loadUser());
    }
}
