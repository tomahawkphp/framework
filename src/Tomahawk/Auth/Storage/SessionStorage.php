<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Auth\Storage;

use Tomahawk\Session\SessionInterface;

/**
 * Class SessionStorage
 *
 * @package Tomahawk\Auth\Storage
 */
class SessionStorage implements StorageInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Set identifier of authorized user
     *
     * @param $storageKey
     * @param $id
     * @return void
     */
    public function setIdentifier($storageKey, $id)
    {
        $this->session->set($storageKey, $id);
    }

    /**
     * Get identifier of authorized user
     *
     * @param $storageKey
     * @return mixed|null
     */
    public function getIdentifier($storageKey)
    {
        return $this->session->get($storageKey);
    }

    /**
     * Remove identifier
     *
     * @param $storageKey
     * @return void
     */
    public function removeIdentifier($storageKey)
    {
        $this->session->remove($storageKey);
    }
}
