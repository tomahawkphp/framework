<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DI\Container;
use Tomahawk\Bundle\FrameworkBundle\DI\ConfigProvider;

class ConfigProviderTest extends TestCase
{
    public function testProvider()
    {
        $container = new Container();
        $configProvider = new ConfigProvider();
        $configProvider->register($container);
    }
}
