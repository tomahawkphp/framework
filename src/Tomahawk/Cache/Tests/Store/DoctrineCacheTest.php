<?php

namespace Tomahawk\Cache\Tests;

use Doctrine\Common\Cache\ArrayCache;
use PHPUnit\Framework\TestCase;
use Tomahawk\Cache\Store\ArrayStore as CacheStore;

class DoctrineCacheTest extends TestCase
{
    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testCache()
    {
        $provider = new ArrayCache();
        $cacheStore = new CacheStore($provider);


        $this->assertTrue($cacheStore->set('another', 'foo'));
        $this->assertEquals('foo', $cacheStore->get('another'));
        $this->assertTrue($cacheStore->has('another'));
        $this->assertFalse($cacheStore->has('bar'));
        $this->assertTrue($cacheStore->setMultiple(['name' => 'Tom', 'age' => 31]));
        $this->assertEquals(['name' => 'Tom', 'age' => 31], $cacheStore->getMultiple(['name', 'age']));
        $this->assertEquals(null, $cacheStore->getMultiple(['none1', 'none2']));
        $this->assertTrue($cacheStore->deleteMultiple(['name', 'age']));
        $this->assertTrue($cacheStore->delete('another'));
        $this->assertTrue($cacheStore->clear());
    }

    /**
     * @expectedException \Psr\SimpleCache\InvalidArgumentException
     */
    public function testExceptionThrowing()
    {
        $provider = new ArrayCache();
        $cacheStore = new CacheStore($provider);

        $cacheStore->set(null, 'foo');
    }

    /**
     * @expectedException \Psr\SimpleCache\InvalidArgumentException
     */
    public function testExceptionThrowingWithArrayKeys()
    {
        $provider = new ArrayCache();
        $cacheStore = new CacheStore($provider);

        $cacheStore->setMultiple(null);
    }



}
