<?php

namespace Tomahawk\Bundle\MonologBundle\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\Bundle\MonologBundle\MonologBundle;

class MonologBundleTest extends TestCase
{
    public function testBundle()
    {
        $container = $this->getMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('register');

        $monologBundle = new MonologBundle();
        $monologBundle->setContainer($container);
        $monologBundle->boot();

    }
}
