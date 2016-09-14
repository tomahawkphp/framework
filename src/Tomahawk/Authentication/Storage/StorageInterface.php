<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Authentication\Storage;

/**
 * Interface StorageInterface
 *
 * @package Tomahawk\Authenticationentication\Storage
 */
interface StorageInterface
{
    /**
     * Set identifier of authorized user
     *
     * @param $storageKey
     * @param $id
     * @return void
     */
    public function setIdentifier($storageKey, $id);

    /**
     * Get identifier of authorized user
     *
     * @param $storageKey
     * @return mixed|null
     */
    public function getIdentifier($storageKey);

    /**
     * Remove identifier
     *
     * @param $storageKey
     * @return void
     */
    public function removeIdentifier($storageKey);
}
