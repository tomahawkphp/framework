<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DI\Container;
use Tomahawk\Bundle\FrameworkBundle\DI\AuthProvider;

class AuthProviderTest extends TestCase
{
    /**
     * @covers \Tomahawk\Bundle\FrameworkBundle\DI\AuthProvider
     */
    public function testProvider()
    {
        $container = $this->getContainer();
        $authProvider = new AuthProvider();
        $authProvider->register($container);

        $this->assertTrue($container->has('auth_handler'));
        $this->assertTrue($container->has('eloquent_auth_handler'));
        $this->assertTrue($container->has('database_auth_handler'));
        $this->assertTrue($container->has('Tomahawk\Auth\AuthInterface'));
        $this->assertTrue($container->hasAlias('auth'));


        $this->assertInstanceOf('Tomahawk\Auth\Auth', $container->get('auth'));
    }

    protected function getContainer()
    {
        $container = new Container();
        $container->set('config', $this->getConfig());
        $container->set('hasher', $this->getMock('Tomahawk\Hashing\HasherInterface'));
        $container->set('session', $this->getMock('Tomahawk\Session\SessionInterface'));

        return $container;
    }

    protected function getConfig()
    {
        $config = $this->getMock('Tomahawk\Config\ConfigInterface');

        $config->method('get')
            ->will($this->returnValueMap(array(
                array('security.handler', null, 'eloquent'),
                array('security.handlers.eloquent', null, array(
                    'model' => 'User'
                )),
            )));

        return $config;
    }
}
