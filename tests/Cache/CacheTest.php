<?php

use Tomahawk\Cache\CacheManager;

class CacheTest extends PHPUnit_Framework_TestCase
{
    public function testArrayCache()
    {
        $config = array(
            'driver' => 'array'
        );

        $cache = new CacheManager($config);

        $cache->save('name', 'Tom');

        $this->assertEquals('Tom', $cache->fetch('name'));
    }

    public function testMemcacheCache()
    {
        /*$config = array(
            'driver' => 'memcache',

            'memcache' => array(
                'host' => 'localhost',
                'port' => 11211
            )
        );

        $cache = new CacheManager($config);

        $cache->save('name', 'Tom');

        $this->assertEquals('Tom', $cache->fetch('name'));*/
    }

}