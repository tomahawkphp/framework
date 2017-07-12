<?php

namespace Tomahawk\Bundle\SwiftmailerBundle\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\Bundle\SwiftmailerBundle\SwiftmailerBundle;

class BundleTest extends TestCase
{
    public function testBundle()
    {
        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');

        $bundle = new SwiftmailerBundle();
        $bundle->setContainer($container);

        $bundle->boot();
    }
}
