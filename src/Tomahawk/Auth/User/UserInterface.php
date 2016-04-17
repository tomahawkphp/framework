<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Auth\User;

/**
 * Interface UserInterface
 *
 * @package Tomahawk\Auth\User
 */
interface UserInterface
{
    /**
     * Get users username
     *
     * @return string
     */
    public function getUsername();

    /**
     * Get users password
     *
     * @return string
     */
    public function getPassword();

    /**
     * Get users salt
     *
     * This can return null if password was encoded without a salt
     *
     * @return string
     */
    public function getSalt();
}
