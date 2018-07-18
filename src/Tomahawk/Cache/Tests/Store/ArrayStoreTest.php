<?php

namespace Tomahawk\Cache\Tests;

use Doctrine\Common\Cache\ArrayCache;
use PHPUnit\Framework\TestCase;
use Tomahawk\Cache\Store\ArrayStore as CacheStore;

class ArrayStoreTest extends TestCase
{
    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testCache()
    {
        $provider = $this->getProvider();
        $cacheStore = new CacheStore($provider);

        $this->assertEquals('array', $cacheStore->getName());

    }

    protected function getProvider()
    {
        return $this->createMock(ArrayCache::class);
    }
}
