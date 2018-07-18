<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Cache\Store;

use Doctrine\Common\Cache\ArrayCache;

/**
 * Class ArrayStore
 * @package Tomahawk\Cache\Driver
 */
class ArrayStore implements CacheStoreInterface
{
    use DoctrineTrait;

    /**
     * ArrayDriver constructor.
     * @param ArrayCache $arrayCache
     */
    public function __construct(ArrayCache $arrayCache)
    {
        $this->driver = $arrayCache;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'array';
    }
}
