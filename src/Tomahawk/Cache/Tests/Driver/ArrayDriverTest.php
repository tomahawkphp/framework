<?php

namespace Tomahawk\Cache\Tests\Driver;

use Doctrine\Common\Cache\ArrayCache;
use PHPUnit\Framework\TestCase;
use Tomahawk\Cache\Driver\ArrayDriver;

/**
 * Class ArrayDriverTest
 * @package Tomahawk\Cache\Tests\Driver
 */
class ArrayDriverTest extends TestCase
{
    public function testGetNameReturnsArray()
    {
        $cache = $this->getCacheMock();

        $driver = new ArrayDriver($cache);

        $this->assertEquals('array', $driver->getName());
    }

    public function testArrayCacheSavesAndReturnsTrue()
    {
        $cache = $this->getCacheMock();

        $cache->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));

        $driver = new ArrayDriver($cache);

        $this->assertTrue($driver->save('foo', 'bar'));
    }

    public function testArrayCacheSavesAndReturnsFalse()
    {
        $cache = $this->getCacheMock();

        $cache->expects($this->once())
            ->method('save')
            ->will($this->returnValue(false));

        $driver = new ArrayDriver($cache);

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

        $driver = new ArrayDriver($cache);

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

        $driver = new ArrayDriver($cache);

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

        $driver = new ArrayDriver($cache);

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

        $driver = new ArrayDriver($cache);

        $driver->save('foo', 'bar');
        $driver->save('baz', 'boom');

        $driver->flush();

        $this->assertFalse($driver->contains('foo'));
        $this->assertFalse($driver->contains('baz'));
    }

    protected function getCacheMock()
    {
        $cache = $this->getMockBuilder(ArrayCache::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $cache;
    }
}
