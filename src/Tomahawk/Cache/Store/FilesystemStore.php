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

use Doctrine\Common\Cache\FilesystemCache;

/**
 * Class FilesystemStore
 * @package Tomahawk\Cache\Driver
 */
class FilesystemStore implements CacheStoreInterface
{
    use DoctrineTrait;

    public function __construct(FilesystemCache $filesystemCache)
    {
        $this->driver = $filesystemCache;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'filesystem';
    }
}
