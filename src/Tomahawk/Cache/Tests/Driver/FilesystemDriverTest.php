<?php

namespace Tomahawk\Cache\Drive\Tests;

use Doctrine\Common\Cache\FilesystemCache;
use Tomahawk\Cache\Driver\FilesystemDriver;
use PHPUnit\Framework\TestCase;

class FilesystemDriverTest extends TestCase
{
    public function testGetNameReturnsArray()
    {
        $cache = $this->getCacheMock();

        $driver = new FilesystemDriver($cache);

        $this->assertEquals('filesystem', $driver->getName());
    }

    public function testArrayCacheSavesAndReturnsTrue()
    {
        $cache = $this->getCacheMock();
        
        $cache->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));

        $driver = new FilesystemDriver($cache);
        
        $this->assertTrue($driver->save('foo', 'bar'));
    }

    public function testArrayCacheSavesAndReturnsFalse()
    {
        $cache = $this->getCacheMock();

        $cache->expects($this->once())
            ->method('save')
            ->will($this->returnValue(false));

        $driver = new FilesystemDriver($cache);

        $this->assertFalse($driver->save('foo', 'bar'));
    }

    public function testContains()
    {
        $cache = $this->getCacheMock();

        $cache->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));

        $cache->expects($this->once())
            ->method('contains')
            ->will($this->returnValue(true));

        $driver = new FilesystemDriver($cache);

        $driver->save('foo', 'bar');

        $this->assertTrue($driver->contains('foo'));
    }

    public function testFetch()
    {
        $cache = $this->getCacheMock();

        $cache->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));

        $cache->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue('bar'));

        $driver = new FilesystemDriver($cache);

        $driver->save('foo', 'bar');

        $this->assertEquals('bar', $driver->fetch('foo'));
    }

    public function testDelete()
    {
        $cache = $this->getCacheMock();

        $cache->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));

        $cache->expects($this->once())
            ->method('delete');

        $cache->expects($this->once())
            ->method('contains')
            ->will($this->returnValue(false));

        $driver = new FilesystemDriver($cache);

        $driver->save('foo', 'bar');

        $driver->delete('foo');

        $this->assertFalse($driver->contains('foo'));
    }

    public function testFlush()
    {
        $cache = $this->getCacheMock();

        $cache->expects($this->exactly(2))
            ->method('save')
            ->will($this->returnValue(true));

        $cache->expects($this->once())
            ->method('flushAll');

        $cache->expects($this->exactly(2))
            ->method('contains')
            ->will($this->returnValue(false));

        $driver = new FilesystemDriver($cache);

        $driver->save('foo', 'bar');
        $driver->save('baz', 'boom');

        $driver->flush();

        $this->assertFalse($driver->contains('foo'));
        $this->assertFalse($driver->contains('baz'));
    }

    protected function getCacheMock()
    {
        $cache = $this->getMockBuilder('Doctrine\Common\Cache\FilesystemCache')
            ->disableOriginalConstructor()
            ->getMock();

        return $cache;
    }

}
