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
 * Interface UserProviderInterface
 *
 * @package Tomahawk\Auth\User
 */
interface UserProviderInterface
{
    /**
     * Find user by username
     *
     * @param $username
     * @return UserInterface|null
     */
    public function findUserByUsername($username);
}
