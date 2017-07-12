<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Tomahawk\Bundle\DoctrineBundle\Mapping\ClassMetadataCollection;
use PHPUnit_Framework_TestCase as TestCase;

class ClassMetadataCollectionTest extends TestCase
{
    public function testGetMetadata()
    {
        $class = new ClassMetadataInfo(__CLASS__);
        $collection = new ClassMetadataCollection(array($class));

        $this->assertEquals(array($class), $collection->getMetadata());
    }

    public function testSetGetPath()
    {
        $class = new ClassMetadataInfo(__CLASS__);
        $collection = new ClassMetadataCollection(array($class));
        $collection->setPath('/path');

        $this->assertEquals('/path', $collection->getPath());
    }

    public function testSetGetNamespace()
    {
        $class = new ClassMetadataInfo(__CLASS__);
        $collection = new ClassMetadataCollection(array($class));
        $collection->setNamespace('MyBundle');

        $this->assertEquals('MyBundle', $collection->getNamespace());
    }
}
