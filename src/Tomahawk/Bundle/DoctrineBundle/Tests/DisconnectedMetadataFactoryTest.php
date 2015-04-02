<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Tomahawk\Bundle\DoctrineBundle\Mapping\ClassMetadataCollection;
use Tomahawk\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory;
use Tomahawk\Test\TestCase;

class DisconnectedMetadataFactoryTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        if (!class_exists('Doctrine\\ORM\\Version')) {
            $this->markTestSkipped('Doctrine ORM is not available.');
        }
    }

    public function testFindNamespaceAndPathForMetadata()
    {
        $class = new ClassMetadataInfo(__CLASS__);
        $collection = new ClassMetadataCollection(array($class));
        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $factory = new DisconnectedMetadataFactory($registry);
        $factory->findNamespaceAndPathForMetadata($collection);

        $this->assertEquals('Tomahawk\Bundle\DoctrineBundle\Tests', $collection->getNamespace());
    }

    protected function getBundleMock()
    {
        $bundle = $this->getMock('Tomahawk\HttpKernel\Bundle\BundleInterface');

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
