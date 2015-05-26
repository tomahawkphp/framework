<?php

namespace Tomahawk\Bundle\CSRFBundle\Test;

use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\CSRFBundle\CSRFBundle;

class CSRFBundleTest extends TestCase
{
    public function testBundle()
    {
        $container = $this->getContainer();

        $bundle = new CSRFBundle();
        $bundle->setContainer($container);
        $bundle->boot();
    }

    protected function getContainer()
    {
        $container = $this->getMock('Tomahawk\DI\ContainerInterface');

        $container->expects($this->once())
            ->method('register');

        return $container;
    }
}
