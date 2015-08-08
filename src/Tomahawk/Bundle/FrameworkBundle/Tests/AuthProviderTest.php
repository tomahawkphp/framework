<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DI\Container;
use Tomahawk\Bundle\FrameworkBundle\DI\AuthProvider;

class AuthProviderTest extends TestCase
{
    public function testProvider()
    {
        $container = new Container();
        $authProvider = new AuthProvider();
        $authProvider->register($container);

        $this->assertTrue($container->has('auth_handler'));
        $this->assertTrue($container->has('eloquent_auth_handler'));
        $this->assertTrue($container->has('database_auth_handler'));
        $this->assertTrue($container->has('Tomahawk\Auth\AuthInterface'));
        $this->assertTrue($container->hasAlias('auth'));
        $this->assertTrue($container->hasAlias('auth'));
    }
}
