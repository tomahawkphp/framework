<?php

namespace Tomahawk\Bundle\MonologBundle\Tests;

use PHPUnit\Framework\TestCase;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\Bundle\MonologBundle\MonologBundle;

class MonologBundleTest extends TestCase
{
    public function testBundle()
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $container->expects($this->once())
            ->method('register');

        $monologBundle = new MonologBundle();
        $monologBundle->setContainer($container);
        $monologBundle->boot();

    }
}
