<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use PHPUnit\Framework\TestCase;
use Tomahawk\Bundle\DoctrineBundle\DoctrineBundle;

class DoctrineBundleTest extends TestCase
{
    public function testBootBundle()
    {
        $container = $this->getContainerMock();
        $container->expects($this->at(0))->method('register');

        $doctrineBundle = new DoctrineBundle();
        $doctrineBundle->setContainer($container);
        $doctrineBundle->boot();
    }

    public function getContainerMock()
    {
        return $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
    }
}
