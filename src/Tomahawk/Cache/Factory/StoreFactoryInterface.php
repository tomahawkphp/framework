<?php

namespace Tomahawk\Cache\Factory;

use Tomahawk\Cache\CacheInterface;

/**
 * Class StoreFactoryInterface
 * @package Tomahawk\Cache\Factory
 */
interface StoreFactoryInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @param array $config
     * @return CacheInterface
     */
    public function make(string $name, array $config = []);
}
