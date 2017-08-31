<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests\Mapping;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Tomahawk\Bundle\DoctrineBundle\Mapping\ClassMetadataCollection;
use Tomahawk\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory;
use PHPUnit\Framework\TestCase;

class DisconnectedMetadataFactoryTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        if (!class_exists('Doctrine\\ORM\\Version')) {
            $this->markTestSkipped('Doctrine ORM is not available.');
        }
    }

    public function testGetClassMetadata()
    {
        $eventManager = $this->getMockBuilder('Doctrine\Common\EventManager')->getMock();

        $driver = $this->getMockBuilder('Doctrine\Common\Persistence\Mapping\Driver\MappingDriver')->getMock();

        $driver->expects($this->any())
            ->method('isTransient')
            ->will($this->returnValue(false));

        $configuration = $this->getMockBuilder('Doctrine\ORM\Configuration')->getMock();

        $configuration->expects($this->any())
            ->method('getMetadataDriverImpl')
            ->will($this->returnValue($driver));

        $manager = $this->getMockBuilder('Doctrine\ORM\EntityManagerInterface')->getMock();

        $manager->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($configuration));

        $manager->expects($this->any())
            ->method('getEventManager')
            ->will($this->returnValue($eventManager));

        $registry = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')->getMock();

        $registry->expects($this->any())
            ->method('getManagers')
            ->will($this->returnValue([$manager]));

        $factory = new DisconnectedMetadataFactory($registry);

        $collection = $factory->getClassMetadata(__CLASS__);

        $this->assertEquals('Tomahawk\Bundle\DoctrineBundle\Tests\Mapping', $collection->getNamespace());
    }

    /**
     * @expectedException \Doctrine\ORM\Mapping\MappingException
     */
    public function testGetClassMetadataThrowsException()
    {
        $eventManager = $this->getMockBuilder('Doctrine\Common\EventManager')->getMock();

        $driver = $this->getMockBuilder('Doctrine\Common\Persistence\Mapping\Driver\MappingDriver')->getMock();

        $driver->expects($this->any())
            ->method('isTransient')
            ->will($this->returnValue(true));

        $configuration = $this->getMockBuilder('Doctrine\ORM\Configuration')->getMock();

        $configuration->expects($this->any())
            ->method('getMetadataDriverImpl')
            ->will($this->returnValue($driver));

        $manager = $this->getMockBuilder('Doctrine\ORM\EntityManagerInterface')->getMock();

        $manager->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($configuration));

        $manager->expects($this->any())
            ->method('getEventManager')
            ->will($this->returnValue($eventManager));

        $registry = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')->getMock();

        $registry->expects($this->any())
            ->method('getManagers')
            ->will($this->returnValue([$manager]));

        $factory = new DisconnectedMetadataFactory($registry);

        $factory->getClassMetadata(__CLASS__);
    }

    public function testFindNamespaceAndPathForMetadata()
    {
        $class = new ClassMetadataInfo('\Vendor\Package\Class');
        $collection = new ClassMetadataCollection(array($class));
        $registry = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')->getMock();
        $factory = new DisconnectedMetadataFactory($registry);
        $factory->findNamespaceAndPathForMetadata($collection, '/path/to/code');
        $this->assertEquals('\Vendor\Package', $collection->getNamespace());
    }

    protected function getBundleMock()
    {
        $bundle = $this->getMockBuilder('Tomahawk\HttpKernel\Bundle\BundleInterface')->getMock();

        $bundle->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('UserBundle'));

        $bundle->expects($this->any())
            ->method('getNamespace')
            ->will($this->returnValue('MyCompany\\Bundle\\UserBundle'));

        $bundle->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('/code/mycompany'));

        return $bundle;
    }
}
