<?php

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
    public function deleteAll();
}