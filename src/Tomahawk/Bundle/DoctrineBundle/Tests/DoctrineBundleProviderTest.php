<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Tomahawk\DI\Container;
use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\DoctrineBundle\DI\DoctrineProvider;

class DoctrineBundleProviderTest extends TestCase
{
    public function testProviderAddsDoctrineToContainer()
    {
        $container = $this->getContainer();
        $provider = new DoctrineProvider();
        $provider->register($container);

        $this->assertTrue($container->has('doctrine.entitymanager'));
    }

    protected function getContainer()
    {
        $container = new Container();

        return $container;
    }

}
