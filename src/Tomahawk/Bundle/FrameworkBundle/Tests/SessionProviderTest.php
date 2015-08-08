<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DI\Container;
use Tomahawk\Bundle\FrameworkBundle\DI\SessionProvider;

class SessionProviderTest extends TestCase
{
    public function testProvider()
    {
        $container = new Container();
        $sessionProvider = new SessionProvider();
        $sessionProvider->register($container);
    }
}
