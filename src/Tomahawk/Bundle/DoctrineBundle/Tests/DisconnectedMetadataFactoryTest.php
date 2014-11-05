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
        /*$this->setExpectedException('RuntimeException', 'Can\'t find base path for "Doctrine\Bundle\DoctrineBundle\Tests\Mapping\DisconnectedMetadataFactoryTest');
        $class = new ClassMetadataInfo(__CLASS__);
        $collection = new ClassMetadataCollection(array($class));
        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $factory = new DisconnectedMetadataFactory($registry);
        $factory->findNamespaceAndPathForMetadata($collection);*/
    }
}
