<?php

namespace Tomahawk\Cache\Tests;

use Doctrine\Common\Cache\FilesystemCache;
use PHPUnit\Framework\TestCase;
use Tomahawk\Cache\Store\FilesystemStore as CacheStore;

class FilesystemStoreTest extends TestCase
{
    public function testCache()
    {
        $cacheStore = new CacheStore($this->getProvider());

        $this->assertEquals('filesystem', $cacheStore->getName());
    }

    protected function getProvider()
    {
        return $this->createMock(FilesystemCache::class);
    }
}
