<?php

namespace Tomahawk\Cache\Test\Factory;

use Tomahawk\Cache\CacheInterface;
use Tomahawk\Cache\Factory\StoreFactoryInterface;
use Tomahawk\Cache\Test\Store\TestStore;

/**
 * Class TestStoreFactory
 * @package Tomahawk\Cache\Test\Factory
 */
class TestStoreFactory implements StoreFactoryInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'test';
    }

    /**
     * @param string $name
     * @param array $config
     * @return CacheInterface
     */
    public function make(string $name, array $config = [])
    {
        return new TestStore();
    }
}
