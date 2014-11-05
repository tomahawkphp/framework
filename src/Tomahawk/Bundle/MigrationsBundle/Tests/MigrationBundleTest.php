<?php

namespace Tomahawk\Bundle\MigrationsBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\MigrationsBundle\MigrationsBundle;

class MigrationBundleTest extends TestCase
{
    public function testBundle()
    {
        $container = $this->getContainer();
        $container->expects($this->exactly(3))
            ->method('set');

        $migrationBundle = new MigrationsBundle();
        $migrationBundle->setContainer($container);
        $migrationBundle->boot();
    }

    public function getContainer()
    {
        $container = $this->getMock('Tomahawk\DI\ContainerInterface');
        return $container;
    }
}
