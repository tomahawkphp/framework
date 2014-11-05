<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Tomahawk\DI\Container;
use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\DoctrineBundle\Registry;

class RegistryTest extends TestCase
{
    public function testGetDefaultConnectionName()
    {
        $container = $this->getMock('Tomahawk\DI\ContainerInterface');
        $registry = new Registry($container, array(), array(), 'default', 'default');
        $this->assertEquals('default', $registry->getDefaultConnectionName());
    }
    public function testGetDefaultEntityManagerName()
    {
        $container = $this->getMock('Tomahawk\DI\ContainerInterface');
        $registry = new Registry($container, array(), array(), 'default', 'default');
        $this->assertEquals('default', $registry->getDefaultManagerName());
    }
    public function testGetDefaultConnection()
    {
        $conn = $this->getMock('Doctrine\DBAL\Connection', array(), array(), '', false);
        $container = $this->getMock('Tomahawk\DI\ContainerInterface');
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
        $container = $this->getMock('Tomahawk\DI\ContainerInterface');
        $container->expects($this->once())
            ->method('get')
            ->with($this->equalTo('doctrine.dbal.default_connection'))
            ->will($this->returnValue($conn));
        $registry = new Registry($container, array('default' => 'doctrine.dbal.default_connection'), array(), 'default', 'default');
        $this->assertSame($conn, $registry->getConnection('default'));
    }
    public function testGetUnknownConnection()
    {
        $container = $this->getMock('Tomahawk\DI\ContainerInterface');
        $registry = new Registry($container, array(), array(), 'default', 'default');
        $this->setExpectedException('InvalidArgumentException', 'Doctrine ORM Connection named "default" does not exist.');
        $registry->getConnection('default');
    }
    public function testGetConnectionNames()
    {
        $container = $this->getMock('Tomahawk\DI\ContainerInterface');
        $registry = new Registry($container, array('default' => 'doctrine.dbal.default_connection'), array(), 'default', 'default');
        $this->assertEquals(array('default' => 'doctrine.dbal.default_connection'), $registry->getConnectionNames());
    }
    public function testGetDefaultEntityManager()
    {
        $em = new \stdClass();
        $container = $this->getMock('Tomahawk\DI\ContainerInterface');
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
        $container = $this->getMock('Tomahawk\DI\ContainerInterface');
        $container->expects($this->once())
            ->method('get')
            ->with($this->equalTo('doctrine.orm.default_entity_manager'))
            ->will($this->returnValue($em));
        $registry = new Registry($container, array(), array('default' => 'doctrine.orm.default_entity_manager'), 'default', 'default');
        $this->assertSame($em, $registry->getManager('default'));
    }
    public function testGetUnknownEntityManager()
    {
        $container = $this->getMock('Tomahawk\DI\ContainerInterface');
        $registry = new Registry($container, array(), array(), 'default', 'default');
        $this->setExpectedException('InvalidArgumentException', 'Doctrine ORM Manager named "default" does not exist.');
        $registry->getManager('default');
    }
    public function testResetDefaultEntityManager()
    {
        $container = $this->getMock('Tomahawk\DI\ContainerInterface');
        $container->expects($this->once())
            ->method('set')
            ->with($this->equalTo('doctrine.orm.default_entity_manager'), $this->equalTo(null));
        $registry = new Registry($container, array(), array('default' => 'doctrine.orm.default_entity_manager'), 'default', 'default');
        $registry->resetManager();
    }
    public function testResetEntityManager()
    {
        $container = $this->getMock('Tomahawk\DI\ContainerInterface');
        $container->expects($this->once())
            ->method('set')
            ->with($this->equalTo('doctrine.orm.default_entity_manager'), $this->equalTo(null));
        $registry = new Registry($container, array(), array('default' => 'doctrine.orm.default_entity_manager'), 'default', 'default');
        $registry->resetManager('default');
    }
    public function testResetUnknownEntityManager()
    {
        $container = $this->getMock('Tomahawk\DI\ContainerInterface');
        $registry = new Registry($container, array(), array(), 'default', 'default');
        $this->setExpectedException('InvalidArgumentException', 'Doctrine ORM Manager named "default" does not exist.');
        $registry->resetManager('default');
    }
}
