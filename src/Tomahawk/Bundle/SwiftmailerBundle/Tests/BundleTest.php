<?php

namespace Tomahawk\Bundle\SwiftmailerBundle\Tests;

use PHPUnit\Framework\TestCase;
use Tomahawk\Bundle\SwiftmailerBundle\SwiftmailerBundle;

class BundleTest extends TestCase
{
    public function testBundle()
    {
        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();

        $bundle = new SwiftmailerBundle();
        $bundle->setContainer($container);

        $bundle->boot();
    }
}
