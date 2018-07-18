<?php

namespace Tomahawk\Cache\Tests;

use PHPUnit\Framework\TestCase;
use Tomahawk\Cache\CacheManager;
use Tomahawk\Cache\Factory\StoreFactory;
use Tomahawk\Cache\Store\CacheStoreInterface;

class CacheManagerTest extends TestCase
{
    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testCacheManager()
    {
        $cacheStore = $this->createMock(CacheStoreInterface::class);

        $cacheStore->expects($this->once())
            ->method('get')
        ;

        $cacheStore->expects($this->once())
            ->method('set')
        ;

        $cacheStore->expects($this->once())
            ->method('has')
        ;

        $cacheStore->expects($this->once())
            ->method('setMultiple')
        ;

        $cacheStore->expects($this->once())
            ->method('getMultiple')
        ;

        $cacheStore->expects($this->once())
            ->method('deleteMultiple')
        ;

        $cacheStore->expects($this->once())
            ->method('delete')
        ;

        $cacheStore->expects($this->once())
            ->method('clear')
        ;

        $storeFactory = $this->createMock(StoreFactory::class);

        $storeFactory->expects($this->once())
            ->method('make')
            ->willReturn($cacheStore)
        ;

        $cacheManager = new CacheManager($storeFactory, 'array');

        $cacheManager->get('something');
        $cacheManager->set('another', 'foo');
        $cacheManager->has('another');
        $cacheManager->setMultiple(['name' => 'Tom', 'age' => 31]);
        $cacheManager->getMultiple(['name', 'age']);
        $cacheManager->deleteMultiple(['name', 'age']);
        $cacheManager->delete('another');
        $cacheManager->clear();
    }
}
