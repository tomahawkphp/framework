<?php

namespace Tomahawk\Bundle\GeneratorBundle\Tests;

use Tomahawk\Bundle\GeneratorBundle\GeneratorBundle;
use Tomahawk\Test\TestCase;

class GeneratorBundleTest extends TestCase
{
    public function testGeneratorBundleBootAddsToContainer()
    {
        $container = $this->getContainerMock();

        $container->expects($this->exactly(2))
            ->method('set');

        $bundle = new GeneratorBundle();
        $bundle->setContainer($container);
        $bundle->boot();
    }

    public function getContainerMock()
    {
        $container = $this->getMockBuilder('Tomahawk\DI\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $container;
    }
}