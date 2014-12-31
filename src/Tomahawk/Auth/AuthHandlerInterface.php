<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Auth;

interface AuthHandlerInterface
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Tomahawk\Auth\UserInterface|null
     */
    public function retrieveById($identifier);

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Tomahawk\Auth\UserInterface|null
     */
    public function retrieveByCredentials(array $credentials);

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Tomahawk\Auth\UserInterface  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserInterface $user, array $credentials);

}

