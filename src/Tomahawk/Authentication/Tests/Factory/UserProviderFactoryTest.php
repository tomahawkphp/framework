<?php

namespace Tomahawk\Authentication\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Tomahawk\Authentication\Factory\UserProviderFactory;
use Tomahawk\Authentication\Test\Factory\TestUserProviderFactory;
use Tomahawk\Authentication\Test\TestUserProvider;
use Tomahawk\Authentication\User\InMemoryUserProvider;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\Container;

/**
 * Class UserProviderFactoryTest
 * @package Tomahawk\Authentication\Tests\Factory
 */
class UserProviderFactoryTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testUserProviderFactory()
    {
        $container = new Container();

        $container->set(InMemoryUserProvider::class, $this->createMock(InMemoryUserProvider::class));

        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->once())
            ->method('get')
            ->with('auth.providers.users')
            ->willReturn([
                'driver' => 'memory',
            ]);

        $factory = new UserProviderFactory(
            $container,
            $configManger,
            []
        );

        $userProvider = $factory->make('users');

        $this->assertInstanceOf(InMemoryUserProvider::class, $userProvider);
    }

    /**
     * @throws \ReflectionException
     */
    public function testUserProviderFactoryWithCustom()
    {
        $container = new Container();

        $container->set(TestUserProviderFactory::class, new TestUserProviderFactory());
        $container->tag(TestUserProviderFactory::class, 'authentication.user_provider.factory');

        $container->set(InMemoryUserProvider::class, $this->createMock(InMemoryUserProvider::class));

        $userProviderServiceIds = $container->findTaggedServiceIds('authentication.user_provider.factory');

        $customUserProviders = [];

        foreach ($userProviderServiceIds as $userProviderServiceId) {
            $customUserProvider = $container->get($userProviderServiceId);
            $customUserProviders[$customUserProvider->getName()] = $customUserProvider;
        }


        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->once())
            ->method('get')
            ->with('auth.providers.users')
            ->willReturn([
                'driver' => 'test',
            ]);

        $factory = new UserProviderFactory(
            $container,
            $configManger,
            $customUserProviders
        );

        $userProvider = $factory->make('users');

        $this->assertInstanceOf(TestUserProvider::class, $userProvider);
    }

    /**
     * @throws \ReflectionException
     * @expectedException \InvalidArgumentException
     */
    public function testUserProviderFactoryWithCustomThrowsExceptionOnNoFactory()
    {
        $container = new Container();

        $container->set(InMemoryUserProvider::class, $this->createMock(InMemoryUserProvider::class));

        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->once())
            ->method('get')
            ->with('auth.providers.users')
            ->willReturn([
                'driver' => 'test',
            ]);

        $factory = new UserProviderFactory(
            $container,
            $configManger,
            []
        );

        $factory->make('users');
    }

    /**
     * @throws \ReflectionException
     * @expectedException \InvalidArgumentException
     */
    public function testUserProviderFactoryWithCustomWithCustomThrowsExceptionOnNoConfig()
    {
        $container = new Container();

        $container->set(TestUserProviderFactory::class, new TestUserProviderFactory());
        $container->tag(TestUserProviderFactory::class, 'authentication.user_provider.factory');

        $container->set(InMemoryUserProvider::class, $this->createMock(InMemoryUserProvider::class));

        $userProviderServiceIds = $container->findTaggedServiceIds('authentication.user_provider.factory');

        $customUserProviders = [];

        foreach ($userProviderServiceIds as $userProviderServiceId) {
            $customUserProvider = $container->get($userProviderServiceId);
            $customUserProviders[$customUserProvider->getName()] = $customUserProvider;
        }


        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->once())
            ->method('get')
            ->with('auth.providers.users')
            ->willReturn(null);

        $factory = new UserProviderFactory(
            $container,
            $configManger,
            $customUserProviders
        );

        $userProvider = $factory->make('users');

        $this->assertInstanceOf(TestUserProvider::class, $userProvider);
    }
}
