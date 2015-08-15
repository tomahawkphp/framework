<?php

namespace Tomahawk\Bundle\MigrationsBundle\Tests;

use Tomahawk\DI\Container;
use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\MigrationsBundle\MigrationsBundle;

class MigrationBundleTest extends TestCase
{
    public function testBundle()
    {
        $container = $this->getContainer();

        $migrationBundle = new MigrationsBundle();
        $migrationBundle->setContainer($container);
        $migrationBundle->boot();

        $this->assertTrue($container->has('migration_repo'));
        $this->assertTrue($container->has('migration_generator'));
        $this->assertTrue($container->has('migrator'));

        $this->assertInstanceOf('Tomahawk\Bundle\MigrationsBundle\Migrator\MigrationRepo', $container->get('migration_repo'));
        $this->assertInstanceOf('Tomahawk\Bundle\MigrationsBundle\Migrator\MigrationGenerator', $container->get('migration_generator'));
        $this->assertInstanceOf('Tomahawk\Bundle\MigrationsBundle\Migrator\Migrator', $container->get('migrator'));
    }

    protected function getContainer()
    {
        $container = new Container();
        $container->set('kernel', $this->getKernel());
        $container->set('illuminate_database', $this->getIlluminateDB());

        return $container;
    }

    protected function getIlluminateDB()
    {
        $manager = $this->getMockBuilder('Illuminate\Database\DatabaseManager')
            ->disableOriginalConstructor()
            ->getMock();

        $db = $this->getMockBuilder('Illuminate\Database\Capsule\Manager')
            ->disableOriginalConstructor()
            ->setMethods(array('getDatabaseManager'))
            ->getMock();

        $db->expects($this->any())
            ->method('getDatabaseManager')
            ->will($this->returnValue($manager));

        return $db;
    }

    protected function getKernel()
    {
        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();

        return $kernel;
    }
}
