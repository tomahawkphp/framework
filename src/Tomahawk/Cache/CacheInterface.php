<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Cache;

interface CacheInterface
{

    /**
     * Fetch an item out of the cache
     *
     * @param $id
     * @return mixed
     */
    public function fetch($id);

    /**
     * Save an item into the cache
     *
     * @param $id
     * @param $value
     * @param bool $lifetime
     * @return bool
     */
    public function save($id, $value, $lifetime = false);

    /**
     * Check if an item has been cached
     *
     * @param $id
     * @return mixed
     */
    public function contains($id);

    /**
     * Delete an item from the cache
     *
     * @param $id
     * @return bool
     */
    public function delete($id);

    /**
     * Delete all items from the cache
     *
     * @return bool
     */
    public function flush();
}
