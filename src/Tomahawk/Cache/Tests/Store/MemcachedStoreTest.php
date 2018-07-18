<?php

namespace Tomahawk\Cache\Tests;

use Doctrine\Common\Cache\MemcachedCache;
use PHPUnit\Framework\TestCase;
use Tomahawk\Cache\Store\MemcachedStore as CacheStore;

class MemcachedStoreTest extends TestCase
{
    public function testCache()
    {
        $cacheStore = new CacheStore($this->getProvider());

        $this->assertEquals('memcached', $cacheStore->getName());
    }

    protected function getProvider()
    {
        return $this->createMock(MemcachedCache::class);
    }
}
