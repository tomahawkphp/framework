<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Tomahawk\DI\Container;
use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\FrameworkBundle\FrameworkBundle;

class FrameworkBundleTest extends TestCase
{
    protected $container;

    public function testBundle()
    {
        $container = new Container();
        $frameworkBundle = new FrameworkBundle();
        $frameworkBundle->setContainer($container);
        $frameworkBundle->boot();

        $this->assertTrue($container->has('route_listener'));
    }
}
