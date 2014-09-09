<?php

namespace Tomahawk\Bundle\GeneratorBundle\Tests;

use Tomahawk\Bundle\GeneratorBundle\GeneratorBundle;
use Tomahawk\DI\Container;
use Tomahawk\Test\TestCase;

class GeneratorBundleTest extends TestCase
{
    public function testGeneratorBundleBootAddsToContainer()
    {
        $container = new Container();

        $bundle = new GeneratorBundle();
        $bundle->setContainer($container);
        $bundle->boot();

        $this->assertInstanceOf('Tomahawk\Bundle\GeneratorBundle\Generator\ModelGenerator', $container->get('model_generator'));
        $this->assertInstanceOf('Tomahawk\Bundle\GeneratorBundle\Generator\ControllerGenerator', $container->get('controller_generator'));
    }

    public function getContainerMock()
    {
        $container = $this->getMockBuilder('Tomahawk\DI\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $container;
    }
}
