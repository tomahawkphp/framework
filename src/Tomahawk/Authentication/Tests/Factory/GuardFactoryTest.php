<?php

namespace Tomahawk\Authentication\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Tomahawk\Authentication\Factory\GuardFactory;
use Tomahawk\Authentication\Factory\UserProviderFactory;
use Tomahawk\Authentication\Test\Factory\TestGuardFactory;
use Tomahawk\Authentication\Guard\SessionGuard;
use Tomahawk\Authentication\Test\Guard\TestGuard;
use Tomahawk\Authentication\User\UserProviderInterface;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Session\SessionInterface;
use Tomahawk\Hashing\HasherInterface;

/**
 * Class GuardFactoryTest
 * @package Tomahawk\Authentication\Tests\Factory
 */
class GuardFactoryTest extends TestCase
{
    /**
     * @throws \ReflectionException
     * @throws \Tomahawk\DependencyInjection\Exception\NotFoundException
     */
    public function testGuardFactory()
    {
        $container = new Container();

        $userProviderFactory = $this->createMock(UserProviderFactory::class);

        $userProviderFactory
            ->method('make')
            ->willReturn($this->createMock(UserProviderInterface::class))
        ;

        $container->set(SessionInterface::class, $this->createMock(SessionInterface::class));
        $container->set(UserProviderInterface::class, $this->createMock(UserProviderInterface::class));
        $container->set(HasherInterface::class, $this->createMock(HasherInterface::class));

        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->once())
            ->method('get')
            ->with('auth.guards.user')
            ->willReturn([
                'driver' => 'session',
                'provider' => 'users',
            ]);

        $guardFactory = new GuardFactory(
            $container,
            $userProviderFactory,
            $configManger,
            []
        );

        $guard = $guardFactory->make('user');

        $this->assertInstanceOf(SessionGuard::class, $guard);
    }

    /**
     * @throws \ReflectionException
     * @throws \Tomahawk\DependencyInjection\Exception\NotFoundException
     */
    public function testCustomGuardFactory()
    {
        $userProviderFactory = $this->createMock(UserProviderFactory::class);

        $userProviderFactory
            ->method('make')
            ->willReturn($this->createMock(UserProviderInterface::class))
        ;

        $container = new Container();

        $container->set(SessionInterface::class, $this->createMock(SessionInterface::class));
        $container->set(UserProviderInterface::class, $this->createMock(UserProviderInterface::class));
        $container->set(HasherInterface::class, $this->createMock(HasherInterface::class));

        $container->set(TestGuardFactory::class, new TestGuardFactory());
        $container->tag(TestGuardFactory::class, 'authentication.guard.factory');

        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->once())
            ->method('get')
            ->with('auth.guards.user')
            ->willReturn([
                'driver' => 'test',
                'provider' => 'users',
            ]);

        $guardServiceIds = $container->findTaggedServiceIds('authentication.guard.factory');

        $customGuards = [];

        foreach ($guardServiceIds as $guardServiceId) {
            $customGuard = $container->get($guardServiceId);
            $customGuards[$customGuard->getName()] = $customGuard;
        }

        $guardFactory = new GuardFactory(
            $container,
            $userProviderFactory,
            $configManger,
            $customGuards
        );

        $guard = $guardFactory->make('user');

        $this->assertInstanceOf(TestGuard::class, $guard);
    }

    /**
     * @throws \ReflectionException
     * @throws \Tomahawk\DependencyInjection\Exception\NotFoundException
     * @expectedException \InvalidArgumentException
     */
    public function testCustomGuardFactoryWithNoConfig()
    {
        $userProviderFactory = $this->createMock(UserProviderFactory::class);

        $container = new Container();

        $container->set(SessionInterface::class, $this->createMock(SessionInterface::class));
        $container->set(UserProviderInterface::class, $this->createMock(UserProviderInterface::class));
        $container->set(HasherInterface::class, $this->createMock(HasherInterface::class));

        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->once())
            ->method('get')
            ->with('auth.guards.user')
            ->willReturn(null)
        ;

        $guardFactory = new GuardFactory(
            $container,
            $userProviderFactory,
            $configManger,
            []
        );

        $guardFactory->make('user');
    }

    /**
     * @throws \ReflectionException
     * @throws \Tomahawk\DependencyInjection\Exception\NotFoundException
     * @expectedException \InvalidArgumentException
     */
    public function testCustomGuardFactoryWithNoFactory()
    {
        $userProviderFactory = $this->createMock(UserProviderFactory::class);

        $container = new Container();

        $container->set(SessionInterface::class, $this->createMock(SessionInterface::class));
        $container->set(UserProviderInterface::class, $this->createMock(UserProviderInterface::class));
        $container->set(HasherInterface::class, $this->createMock(HasherInterface::class));

        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->once())
            ->method('get')
            ->with('auth.guards.user')
            ->willReturn([
                'driver' => 'test',
                'provider' => 'users',
            ]);

        $guardFactory = new GuardFactory(
            $container,
            $userProviderFactory,
            $configManger,
            []
        );

        $guard = $guardFactory->make('user');

        $this->assertInstanceOf(TestGuard::class, $guard);
    }
}
