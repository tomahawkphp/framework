<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Tomahawk\Bundle\FrameworkBundle\DI\FrameworkProvider;
use Tomahawk\Test\TestCase;

class FrameworkProviderTest extends TestCase
{
    public function testProvider()
    {
        $container = $this->getContainer();
        $container->expects($this->atLeastOnce())
            ->method('set');

        $frameworkProvider = new FrameworkProvider();
        $frameworkProvider->register($container);

    }

    public function getContainer()
    {
        $container = $this->getMock('Tomahawk\DI\ContainerInterface');
        return $container;
    }
}

