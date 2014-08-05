<?php

namespace Tomahawk\Cache\Tests;

use Tomahawk\Cache\CacheManager;
use Tomahawk\Test\TestCase;

class CacheManagerTest extends TestCase
{
    public function testConstructorAddsProvider()
    {
        $cache = $this->getCacheProviderMock();
        $cacheFactory = new CacheManager($cache);

        $this->assertInstanceOf('Tomahawk\Cache\Provider\CacheProviderInterface', $cacheFactory->getProvider());
    }

    public function testContains()
    {
        $cache = $this->getCacheProviderMock();

        $cache->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));

        $cache->expects($this->once())
            ->method('contains')
            ->will($this->returnValue(true));

        $cacheFactory = new CacheManager($cache);

        $cacheFactory->save('foo', 'bar');

        $this->assertTrue($cacheFactory->contains('foo'));
    }

    public function testFetch()
    {
        $cache = $this->getCacheProviderMock();

        $cache->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));

        $cache->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue('bar'));

        $cacheFactory = new CacheManager($cache);

        $cacheFactory->save('foo', 'bar');

        $this->assertEquals('bar', $cacheFactory->fetch('foo'));
    }

    public function testDelete()
    {
        $cache = $this->getCacheProviderMock();

        $cache->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));

        $cache->expects($this->once())
            ->method('delete');

        $cache->expects($this->once())
            ->method('contains')
            ->will($this->returnValue(false));

        $cacheFactory = new CacheManager($cache);

        $cacheFactory->save('foo', 'bar');

        $cacheFactory->delete('foo');

        $this->assertFalse($cacheFactory->contains('foo'));
    }

    public function testFlush()
    {
        $cache = $this->getCacheProviderMock();

        $cache->expects($this->exactly(2))
            ->method('save')
            ->will($this->returnValue(true));

        $cache->expects($this->once())
            ->method('flush');

        $cache->expects($this->exactly(2))
            ->method('contains')
            ->will($this->returnValue(false));

        $cacheFactory = new CacheManager($cache);

        $cacheFactory->save('foo', 'bar');
        $cacheFactory->save('baz', 'boom');

        $cacheFactory->flush();

        $this->assertFalse($cacheFactory->contains('foo'));
        $this->assertFalse($cacheFactory->contains('baz'));
    }

    protected function getCacheProviderMock()
    {
        $cache = $this->getMockBuilder('Tomahawk\Cache\Provider\CacheProviderInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $cache->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));

        return $cache;
    }

}