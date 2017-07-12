<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Doctrine\ORM\ORMException;
use Tomahawk\DependencyInjection\Container;
use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\Bundle\DoctrineBundle\Registry;

class RegistryTest extends TestCase
{
    public function testGetDefaultConnectionName()
    {
        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');
        $registry = new Registry($container, array(), array(), 'default', 'default');
        $this->assertEquals('default', $registry->getDefaultConnectionName());
    }

    public function testGetAliasNamespace()
    {
        $configuration = $this->getMock('Doctrine\ORM\Configuration');

        $configuration->expects($this->any())
            ->method('getEntityNamespace')
            ->will($this->returnValue('Path\To\Namespace'));

        $manager = $this->getMock('Doctrine\ORM\EntityManagerInterface');

        $manager->expects($this->once())
            ->method('getConfiguration')
            ->will($this->returnValue($configuration));

        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');

        $container->expects($this->any())
            ->method('get')
            ->with('__manager__')
            ->will($this->returnValue($manager));

        $registry = new Registry($container, array(), array('default' => '__manager__'), 'default', 'default');

        $this->assertEquals('Path\To\Namespace', $registry->getAliasNamespace('foo'));
    }

    /**
     * @expectedException \Doctrine\ORM\ORMException
     */
    public function testGetAliasNamespaceWithManagerThrowsException()
    {
        $configuration = $this->getMock('Doctrine\ORM\Configuration');

        $configuration->expects($this->any())
            ->method('getEntityNamespace')
            ->will($this->returnValue('Path\To\Namespace'));

        $manager = $this->getMock('Doctrine\ORM\EntityManagerInterface');

        $manager->expects($this->once())
            ->method('getConfiguration')
            ->will($this->throwException(new ORMException()));

        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');

        $container->expects($this->any())
            ->method('get')
            ->with('__manager__')
            ->will($this->returnValue($manager));

        $registry = new Registry($container, array(), array('default' => '__manager__'), 'default', 'default');

        $this->assertEquals('Path\To\Namespace', $registry->getAliasNamespace('foo'));
    }

    /**
     * @expectedException \Doctrine\ORM\ORMException
     */
    public function testGetAliasNamespaceThrowsException()
    {
        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');

        $registry = new Registry($container, array(), array(), 'default', 'default');

        $registry->getAliasNamespace('foo');
    }

    public function testGetDefaultEntityManagerName()
    {
        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');
        $registry = new Registry($container, array(), array(), 'default', 'default');
        $this->assertEquals('default', $registry->getDefaultManagerName());
    }

    public function testGetDefaultConnection()
    {
        $conn = $this->getMock('Doctrine\DBAL\Connection', array(), array(), '', false);
        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');
        $container->expects($this->once())
            ->method('get')
            ->with($this->equalTo('doctrine.dbal.default_connection'))
            ->will($this->returnValue($conn));
        $registry = new Registry($container, array('default' => 'doctrine.dbal.default_connection'), array(), 'default', 'default');
        $this->assertSame($conn, $registry->getConnection());
    }

    public function testGetConnection()
    {
        $conn = $this->getMock('Doctrine\DBAL\Connection', array(), array(), '', false);
        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');
        $container->expects($this->once())
            ->method('get')
            ->with($this->equalTo('doctrine.dbal.default_connection'))
            ->will($this->returnValue($conn));
        $registry = new Registry($container, array('default' => 'doctrine.dbal.default_connection'), array(), 'default', 'default');
        $this->assertSame($conn, $registry->getConnection('default'));
    }

    public function testGetUnknownConnection()
    {
        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');
        $registry = new Registry($container, array(), array(), 'default', 'default');
        $this->setExpectedException('InvalidArgumentException', 'Doctrine ORM Connection named "default" does not exist.');
        $registry->getConnection('default');
    }

    public function testGetConnectionNames()
    {
        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');
        $registry = new Registry($container, array('default' => 'doctrine.dbal.default_connection'), array(), 'default', 'default');
        $this->assertEquals(array('default' => 'doctrine.dbal.default_connection'), $registry->getConnectionNames());
    }

    public function testGetDefaultEntityManager()
    {
        $em = new \stdClass();
        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');
        $container->expects($this->once())
            ->method('get')
            ->with($this->equalTo('doctrine.orm.default_entity_manager'))
            ->will($this->returnValue($em));
        $registry = new Registry($container, array(), array('default' => 'doctrine.orm.default_entity_manager'), 'default', 'default');
        $this->assertSame($em, $registry->getManager());
    }

    public function testGetEntityManager()
    {
        $em = new \stdClass();
        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');
        $container->expects($this->once())
            ->method('get')
            ->with($this->equalTo('doctrine.orm.default_entity_manager'))
            ->will($this->returnValue($em));
        $registry = new Registry($container, array(), array('default' => 'doctrine.orm.default_entity_manager'), 'default', 'default');
        $this->assertSame($em, $registry->getManager('default'));
    }

    public function testGetUnknownEntityManager()
    {
        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');
        $registry = new Registry($container, array(), array(), 'default', 'default');
        $this->setExpectedException('InvalidArgumentException', 'Doctrine ORM Manager named "default" does not exist.');
        $registry->getManager('default');
    }

    public function testResetDefaultEntityManager()
    {
        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');
        $container->expects($this->once())
            ->method('set')
            ->with($this->equalTo('doctrine.orm.default_entity_manager'), $this->equalTo(null));
        $registry = new Registry($container, array(), array('default' => 'doctrine.orm.default_entity_manager'), 'default', 'default');
        $registry->resetManager();
    }

    public function testResetEntityManager()
    {
        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');
        $container->expects($this->once())
            ->method('set')
            ->with($this->equalTo('doctrine.orm.default_entity_manager'), $this->equalTo(null));
        $registry = new Registry($container, array(), array('default' => 'doctrine.orm.default_entity_manager'), 'default', 'default');
        $registry->resetManager('default');
    }

    public function testResetUnknownEntityManager()
    {
        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');
        $registry = new Registry($container, array(), array(), 'default', 'default');
        $this->setExpectedException('InvalidArgumentException', 'Doctrine ORM Manager named "default" does not exist.');
        $registry->resetManager('default');
    }

    public function testGetManagers()
    {
        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');
        $registry = new Registry($container, array(), array('default' => 'doctrine.orm.default_entity_manager'), 'default', 'default');
        $this->assertCount(1, $registry->getManagers());
    }

    public function testGetEntityManagerNames()
    {
        $container = $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');
        $registry = new Registry($container, array(), array('default' => 'doctrine.orm.default_entity_manager'), 'default', 'default');
        $this->assertEquals(array('default' => 'doctrine.orm.default_entity_manager'), $registry->getManagerNames());
    }
}
