<?php

namespace Tomahawk\Cache\Provider;

use Tomahawk\Cache\CacheInterface;

interface CacheProviderInterface extends CacheInterface
{
    /**
     * @return string
     */
    public function getName();
}