<?php

namespace Tomahawk\Cache\Tests;

use Doctrine\Common\Cache\PredisCache;
use PHPUnit\Framework\TestCase;
use Tomahawk\Cache\Store\RedisStore as CacheStore;

class RedisStoreTest extends TestCase
{
    public function testCache()
    {
        $cacheStore = new CacheStore($this->getProvider());

        $this->assertEquals('redis', $cacheStore->getName());
    }

    protected function getProvider()
    {
        return $this->createMock(PredisCache::class);
    }
}
