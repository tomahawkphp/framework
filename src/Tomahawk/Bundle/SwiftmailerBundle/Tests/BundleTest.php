<?php

namespace Tomahawk\Bundle\SwiftmailerBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\SwiftmailerBundle\SwiftmailerBundle;


class BundleTest extends TestCase
{
    public function testBundle()
    {
        $container = $this->getMock('Tomahawk\DI\ContainerInterface');

        $bundle = new SwiftmailerBundle();
        $bundle->setContainer($container);

        $bundle->boot();
    }
}