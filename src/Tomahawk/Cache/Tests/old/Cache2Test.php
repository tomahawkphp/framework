<?php

namespace Tomahawk\Cache\Tests;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\MemcacheCache;
use Tomahawk\Cache\CacheFactory;
use Tomahawk\Test\TestCase;
use Tomahawk\Cache\CacheManager;

//use Tomahawk\Cache\Drivers\Arr;

class Cache2Test extends TestCase
{
    public function testArrayCacheExistence()
    {
        //$cache = new CacheFactory();

        //$cache->

        //$cache->addDriver(new Arr($this->getArrayStore()));

        //$cache->use('array');
    }

    /**
     * @return ArrayCache
     */
    public function getArrayStore()
    {
        return new ArrayCache();
    }

}
