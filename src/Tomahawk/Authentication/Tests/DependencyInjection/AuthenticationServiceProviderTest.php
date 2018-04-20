<?php

namespace Tomahawk\Authentication\Tests\DependencyInjection;

use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\TestCase;
use Tomahawk\Authentication\AuthenticationProvider;
use Tomahawk\Authentication\DependencyInjection\AuthenticationServiceProvider;
use Tomahawk\Authentication\Factory\GuardFactory;
use Tomahawk\Authentication\Factory\UserProviderFactory;
use Tomahawk\Authentication\Test\Factory\TestGuardFactory;
use Tomahawk\Authentication\Test\Factory\TestUserProviderFactory;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\Container;

class AuthenticationServiceProviderTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testServiceProvider()
    {
        $container = new Container();

        $configManager = $this->createMock(ConfigInterface::class);

        $configManager
            ->expects($this->once())
            ->method('get')
            ->with('auth.default')
            ->willReturn('user')
        ;

        $container->set(ConfigInterface::class, $configManager);

        $container->set(TestGuardFactory::class, new TestGuardFactory());
        $container->tag(TestGuardFactory::class, 'authentication.guard.factory');


        $container->set(TestUserProviderFactory::class, new TestUserProviderFactory());
        $container->tag(TestUserProviderFactory::class, 'authentication.user_provider.factory');

        $provider = new AuthenticationServiceProvider();

        $provider->register($container);

        $this->assertTrue($container->has(GuardFactory::class));
        $this->assertInstanceOf(GuardFactory::class, $container->get(GuardFactory::class));

        $this->assertTrue($container->has(UserProviderFactory::class));
        $this->assertInstanceOf(UserProviderFactory::class, $container->get(UserProviderFactory::class));

        $this->assertTrue($container->has(AuthenticationProvider::class));
        $this->assertInstanceOf(AuthenticationProvider::class, $container->get(AuthenticationProvider::class));
    }
}
