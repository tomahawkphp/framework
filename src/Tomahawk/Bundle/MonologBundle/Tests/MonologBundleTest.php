<?php

namespace Tomahawk\Bundle\MonologBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DI\Container;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\Bundle\MonologBundle\MonologBundle;

class MonologBundleTest extends TestCase
{
    public function testBundle()
    {
        $container = $this->getMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('register');

        $monologBundle = new MonologBundle();
        $monologBundle->boot();

    }
}
