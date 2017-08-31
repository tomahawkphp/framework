<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Doctrine\ORM\ORMException;
use Tomahawk\DependencyInjection\Container;
use PHPUnit\Framework\TestCase;
use Tomahawk\Bundle\DoctrineBundle\Registry;

class RegistryTest extends TestCase
{
    public function testGetDefaultConnectionName()
    {
        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        $registry = new Registry($container, array(), array(), 'default', 'default');
        $this->assertEquals('default', $registry->getDefaultConnectionName());
    }

    public function testGetAliasNamespace()
    {
        $configuration = $this->getMockBuilder('Doctrine\ORM\Configuration')->getMock();

        $configuration->expects($this->any())
            ->method('getEntityNamespace')
            ->will($this->returnValue('Path\To\Namespace'));

        $manager = $this->getMockBuilder('Doctrine\ORM\EntityManagerInterface')->getMock();

        $manager->expects($this->once())
            ->method('getConfiguration')
            ->will($this->returnValue($configuration));

        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();

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
        $configuration = $this->getMockBuilder('Doctrine\ORM\Configuration')->getMock();

        $configuration->expects($this->any())
            ->method('getEntityNamespace')
            ->will($this->returnValue('Path\To\Namespace'));

        $manager = $this->getMockBuilder('Doctrine\ORM\EntityManagerInterface')->getMock();

        $manager->expects($this->once())
            ->method('getConfiguration')
            ->will($this->throwException(new ORMException()));

        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();

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
        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();

        $registry = new Registry($container, array(), array(), 'default', 'default');

        $registry->getAliasNamespace('foo');
    }

    public function testGetDefaultEntityManagerName()
    {
        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        $registry = new Registry($container, array(), array(), 'default', 'default');
        $this->assertEquals('default', $registry->getDefaultManagerName());
    }

    public function testGetDefaultConnection()
    {
        $conn = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        $container->expects($this->once())
            ->method('get')
            ->with($this->equalTo('doctrine.dbal.default_connection'))
            ->will($this->returnValue($conn));
        $registry = new Registry($container, array('default' => 'doctrine.dbal.default_connection'), array(), 'default', 'default');
        $this->assertSame($conn, $registry->getConnection());
    }

    public function testGetConnection()
    {
        $conn = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        $container->expects($this->once())
            ->method('get')
            ->with($this->equalTo('doctrine.dbal.default_connection'))
            ->will($this->returnValue($conn));
        $registry = new Registry($container, array('default' => 'doctrine.dbal.default_connection'), array(), 'default', 'default');
        $this->assertSame($conn, $registry->getConnection('default'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Doctrine ORM Connection named "default" does not exist.
     */
    public function testGetUnknownConnection()
    {
        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        $registry = new Registry($container, array(), array(), 'default', 'default');
        $registry->getConnection('default');
    }

    public function testGetConnectionNames()
    {
        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        $registry = new Registry($container, array('default' => 'doctrine.dbal.default_connection'), array(), 'default', 'default');
        $this->assertEquals(array('default' => 'doctrine.dbal.default_connection'), $registry->getConnectionNames());
    }

    public function testGetDefaultEntityManager()
    {
        $em = new \stdClass();
        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
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
        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        $container->expects($this->once())
            ->method('get')
            ->with($this->equalTo('doctrine.orm.default_entity_manager'))
            ->will($this->returnValue($em));
        $registry = new Registry($container, array(), array('default' => 'doctrine.orm.default_entity_manager'), 'default', 'default');
        $this->assertSame($em, $registry->getManager('default'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Doctrine ORM Manager named "default" does not exist.
     */
    public function testGetUnknownEntityManager()
    {
        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        $registry = new Registry($container, array(), array(), 'default', 'default');
        $registry->getManager('default');
    }

    public function testResetDefaultEntityManager()
    {
        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        $container->expects($this->once())
            ->method('set')
            ->with($this->equalTo('doctrine.orm.default_entity_manager'), $this->equalTo(null));
        $registry = new Registry($container, array(), array('default' => 'doctrine.orm.default_entity_manager'), 'default', 'default');
        $registry->resetManager();
    }

    public function testResetEntityManager()
    {
        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        $container->expects($this->once())
            ->method('set')
            ->with($this->equalTo('doctrine.orm.default_entity_manager'), $this->equalTo(null));
        $registry = new Registry($container, array(), array('default' => 'doctrine.orm.default_entity_manager'), 'default', 'default');
        $registry->resetManager('default');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Doctrine ORM Manager named "default" does not exist.
     */
    public function testResetUnknownEntityManager()
    {
        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        $registry = new Registry($container, array(), array(), 'default', 'default');
        $registry->resetManager('default');
    }

    public function testGetManagers()
    {
        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        $registry = new Registry($container, array(), array('default' => 'doctrine.orm.default_entity_manager'), 'default', 'default');
        $this->assertCount(1, $registry->getManagers());
    }

    public function testGetEntityManagerNames()
    {
        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        $registry = new Registry($container, array(), array('default' => 'doctrine.orm.default_entity_manager'), 'default', 'default');
        $this->assertEquals(array('default' => 'doctrine.orm.default_entity_manager'), $registry->getManagerNames());
    }
}
