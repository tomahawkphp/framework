<?php

namespace Tomahawk\Cache\Tests;

use Doctrine\Common\Cache\MemcacheCache;
use Tomahawk\Test\TestCase;
use Tomahawk\Cache\CacheManager;

class CacheTest extends TestCase
{
    public function testArrayCacheExistence()
    {
        $config = array(
            'driver' => 'array'
        );

        $cache = new CacheManager($config);

        $cache->save('name', 'Tom');

        $this->assertTrue($cache->contains('name'));
        $this->assertFalse($cache->contains('foo'));

        $this->assertEquals('Tom', $cache->fetch('name'));
    }

    public function testArrayCacheDeleteSingle()
    {
        $config = array(
            'driver' => 'array'
        );

        $cache = new CacheManager($config);

        $cache->save('name', 'Tom');

        $this->assertEquals('Tom', $cache->fetch('name'));

        $cache->delete('name');

        $this->assertFalse($cache->contains('name'));
    }

    public function testArrayCacheDeleteAll()
    {
        $config = array(
            'driver' => 'array'
        );

        $cache = new CacheManager($config);

        $cache->save('name', 'Tom');
        $cache->save('age', 27);

        $this->assertEquals('Tom', $cache->fetch('name'));
        $this->assertEquals(27, $cache->fetch('age'));

        $cache->flush();

        $this->assertFalse($cache->contains('name'));
        $this->assertFalse($cache->contains('age'));
    }

    public function testFilesystemCache()
    {
        $config = array(
            'driver' => 'filesystem',
            'save_path' => __DIR__ .'/cache'
        );

        $cache = new CacheManager($config);

        $cache->save('name', 'Tom');
        $cache->save('age', 27);

        $this->assertEquals('Tom', $cache->fetch('name'));
        $this->assertEquals(27, $cache->fetch('age'));

        $cache->flush();

        $this->assertFalse($cache->contains('name'));
        $this->assertFalse($cache->contains('age'));
    }

    /*public function testApcCache()
    {
        $config = array(
            'driver' => 'apc'
        );

        $cache = new CacheManager($config);

        $cache->save('name', 'Tom');
        $cache->save('age', 27);

        $this->assertEquals('Tom', $cache->fetch('name'));
        $this->assertEquals(27, $cache->fetch('age'));

        $cache->flush();

        $this->assertFalse($cache->contains('name'));
        $this->assertFalse($cache->contains('age'));
    }*/

    public function testMemcache()
    {
        $memcache = new MemcacheCache();
    }

}
