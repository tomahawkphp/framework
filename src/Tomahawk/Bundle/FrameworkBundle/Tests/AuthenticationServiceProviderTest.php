<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use PHPUnit\Framework\TestCase;
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
        $this->assertTrue($container->has('authentication.provider.memory'));

        $this->assertTrue($container->hasAlias('authentication'));
        $this->assertTrue($container->hasAlias('authentication.user.provider'));
        $this->assertTrue($container->hasAlias('authentication.password.encoder'));
        $this->assertTrue($container->hasAlias('authentication.password.encoder.bcrypt'));

        // Try and get provider out of container
        $inMemoryProvider = $container->get('authentication.provider.memory');

        $this->assertInstanceOf('Tomahawk\Authentication\Encoder\PasswordEncoderInterface', $container->get('Tomahawk\Authentication\Encoder\PasswordEncoderInterface'));

        $this->assertInstanceOf('Tomahawk\Authentication\User\InMemoryUserProvider', $inMemoryProvider);

        // Check that users have been added
        $this->assertNotNull($inMemoryProvider->findUserByUsername('tommy'));

        $this->assertInstanceOf('Tomahawk\Authentication\AuthenticationProviderInterface', $container->get('authentication'));
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

        $container->get('authentication.user.provider');
    }

    public function testCustomUserProvider()
    {
        $container = $this->getContainer('my_provider');
        $authenticationServiceProvider = new AuthenticationServiceProvider();
        $authenticationServiceProvider->register($container);

        $container->get('authentication.user.provider');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage User provider "my_non_existent_provider" not registered under "authentication.my_non_existent_provider"
     */
    public function testExistentUserProviderWithNoServiceId()
    {
        $container = $this->getContainer('my_non_existent_provider');
        $authenticationServiceProvider = new AuthenticationServiceProvider();
        $authenticationServiceProvider->register($container);

        $container->get('authentication.user.provider');
    }

    protected function getContainer($defaultProvider = 'memory')
    {
        $container = new Container();
        $container->set('config', $this->getConfig($defaultProvider));
        $container->set('session', $this->getMockBuilder('Tomahawk\Session\SessionInterface')->getMock());
        $container->set('authentication.my_provider', $this->getMockBuilder('Tomahawk\Authentication\User\UserProviderInterface')->getMock());

        return $container;
    }

    protected function getConfig($defaultProvider = 'memory')
    {
        $config = $this->getMockBuilder('Tomahawk\Config\ConfigInterface')->getMock();

        $config->method('get')
            ->will($this->returnValueMap([
                ['security.provider', null, $defaultProvider],
                ['security.providers', null,
                    [
                        'memory' => ['users' => $this->users],
                        'my_provider' => ['service' => 'authentication.my_provider'],
                        'my_non_existent_provider' => ['service' => 'authentication.my_non_existent_provider'],
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
