<?php

namespace Tomahawk\Bundle\GeneratorBundle\Tests;

use Symfony\Component\Filesystem\Filesystem;
use Tomahawk\Bundle\GeneratorBundle\GeneratorBundle;
use Tomahawk\DI\Container;
use Tomahawk\Test\TestCase;

class GeneratorBundleTest extends TestCase
{
    public function testGeneratorBundleBootAddsToContainer()
    {
        $container = new Container();
        $container->set('filesystem', new Filesystem());

        $bundle = new GeneratorBundle();
        $bundle->setContainer($container);
        $bundle->boot();

        $this->assertInstanceOf('Tomahawk\Bundle\GeneratorBundle\Generator\BundleGenerator', $container->get('bundle_generator'));
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
