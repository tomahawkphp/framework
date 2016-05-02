<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\AuthenticationServiceProvider;

class AuthenticationServiceProviderTest extends TestCase
{
    /**
     * @var array
     */
    private $users = [
        'tommy' => [
            'password' => 'mypassword'
        ]
    ];

    /**
     * @covers \Tomahawk\Bundle\FrameworkBundle\DependencyInjection\AuthenticationServiceProvider
     */
    public function testProvider()
    {
        $container = $this->getContainer('memory');
        $authenticationServiceProvider = new AuthenticationServiceProvider();
        $authenticationServiceProvider->register($container);

        $this->assertTrue($container->has('Tomahawk\Authentication\AuthenticationProviderInterface'));
        $this->assertTrue($container->has('Tomahawk\Authentication\User\UserProviderInterface'));
        $this->assertTrue($container->has('Tomahawk\Authentication\Encoder\PasswordEncoderInterface'));
        $this->assertTrue($container->has('auth.provider.memory'));

        $this->assertTrue($container->hasAlias('auth'));
        $this->assertTrue($container->hasAlias('auth.user.provider'));
        $this->assertTrue($container->hasAlias('auth.password.encoder'));
        $this->assertTrue($container->hasAlias('auth.password.encoder.bcrypt'));

        // Try and get provider out of container
        $inMemoryProvider = $container->get('auth.provider.memory');

        $this->assertInstanceOf('Tomahawk\Authentication\Encoder\PasswordEncoderInterface', $container->get('Tomahawk\Authentication\Encoder\PasswordEncoderInterface'));

        $this->assertInstanceOf('Tomahawk\Authentication\User\InMemoryUserProvider', $inMemoryProvider);

        // Check that users have been added
        $this->assertNotNull($inMemoryProvider->findUserByUsername('tommy'));

        $this->assertInstanceOf('Tomahawk\Authentication\AuthenticationProviderInterface', $container->get('auth'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown user provider "foo". Have you added it to the security config and set the "service" parameter?
     */
    public function testNotExistentUserProvider()
    {
        $container = $this->getContainer('foo');
        $authenticationServiceProvider = new AuthenticationServiceProvider();
        $authenticationServiceProvider->register($container);

        $container->get('auth.user.provider');
    }

    public function testCustomUserProvider()
    {
        $container = $this->getContainer('my_provider');
        $authenticationServiceProvider = new AuthenticationServiceProvider();
        $authenticationServiceProvider->register($container);

        $container->get('auth.user.provider');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage User provider "my_non_existent_provider" not registered under "auth.my_non_existent_provider"
     */
    public function testExistentUserProviderWithNoServiceId()
    {
        $container = $this->getContainer('my_non_existent_provider');
        $authenticationServiceProvider = new AuthenticationServiceProvider();
        $authenticationServiceProvider->register($container);

        $container->get('auth.user.provider');
    }

    protected function getContainer($defaultProvider = 'memory')
    {
        $container = new Container();
        $container->set('config', $this->getConfig($defaultProvider));
        $container->set('session', $this->getMock('Tomahawk\Session\SessionInterface'));
        $container->set('auth.my_provider', $this->getMock('Tomahawk\Authentication\User\UserProviderInterface'));

        return $container;
    }

    protected function getConfig($defaultProvider = 'memory')
    {
        $config = $this->getMock('Tomahawk\Config\ConfigInterface');

        $config->method('get')
            ->will($this->returnValueMap([
                ['security.provider', null, $defaultProvider],
                ['security.providers', null,
                    [
                        'memory' => ['users' => $this->users],
                        'my_provider' => ['service' => 'auth.my_provider'],
                        'my_non_existent_provider' => ['service' => 'auth.my_non_existent_provider'],
                    ]
                ],
                ['security.providers.memory', null, [
                        'users' => $this->users
                    ]
                ],
            ]));

        return $config;
    }
}
