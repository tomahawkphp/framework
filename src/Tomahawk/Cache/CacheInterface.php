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
     * @param $id
     * @return mixed
     */
    public function fetch($id);

    /**
     * @param $id
     * @param $value
     * @param bool $lifetime
     * @return bool
     */
    public function save($id, $value, $lifetime = false);

    /**
     * @param $id
     * @return mixed
     */
    public function contains($id);

    /**
     * @param $id
     * @return bool
     */
    public function delete($id);

    /**
     * @return bool
     */
    public function flush();

}
