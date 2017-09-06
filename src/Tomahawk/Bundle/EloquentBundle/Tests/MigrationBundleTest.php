<?php

namespace Tomahawk\Bundle\EloquentBundle\Tests;

use Illuminate\Database\Capsule\Manager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tomahawk\Bundle\EloquentBundle\Migrator\MigrationGenerator;
use Tomahawk\Bundle\EloquentBundle\Migrator\MigrationRepo;
use Tomahawk\Bundle\EloquentBundle\Migrator\Migrator;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\Container;
use PHPUnit\Framework\TestCase;
use Tomahawk\Bundle\EloquentBundle\EloquentBundle;

class MigrationBundleTest extends TestCase
{
    public function testBundle()
    {
        $eventDispatcher = $this->getEventDispatcher();

        $eventDispatcher->expects($this->once())
            ->method('addSubscriber');

        $container = $this->getContainer();

        $migrationBundle = new EloquentBundle();
        $migrationBundle->setContainer($container);
        $migrationBundle->boot();
        $migrationBundle->registerEvents($eventDispatcher);


        $this->assertTrue($container->has(MigrationRepo::class));
        $this->assertTrue($container->has(MigrationGenerator::class));
        $this->assertTrue($container->has(Migrator::class));

        $this->assertInstanceOf(MigrationRepo::class, $container->get(MigrationRepo::class));
        $this->assertInstanceOf(MigrationGenerator::class, $container->get(MigrationGenerator::class));
        $this->assertInstanceOf(Migrator::class, $container->get(Migrator::class));
    }

    protected function getContainer()
    {
        $container = new Container();
        $container->set('kernel', $this->getKernel());
        $container->set('config', $this->getConfig());
        $container->set(Manager::class, $this->getIlluminateDB());

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

    protected function getConfig()
    {
        $config = $this->getMockBuilder(ConfigInterface::class)
            ->getMock();

        $config
            ->method('get')
            ->will($this->returnValueMap([
                ['database.connections', [], [
                    'default' => [
                        'driver'    => 'mysql',
                        'host'      => 'localhost',
                        'port'      => '3306',
                        'database'  => 'test',
                        'username'  => 'root',
                        'password'  => '',
                        'charset'   => 'utf8',
                        'collation' => 'utf8_unicode_ci',
                        'prefix'    => '',
                    ],
                    'laravel' => [
                        'driver'    => 'mysql',
                        'host'      => 'localhost',
                        'port'      => '3306',
                        'database'  => 'laravel',
                        'username'  => 'root',
                        'password'  => '',
                        'charset'   => 'utf8',
                        'collation' => 'utf8_unicode_ci',
                        'prefix'    => '',
                    ]
                ]],
                ['database.default', null, 'default'],
                ['database.fetch', null, \PDO::FETCH_CLASS],
            ]));

        return $config;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEventDispatcher()
    {
        return $this->createMock(EventDispatcherInterface::class);
    }
}
