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

use Tomahawk\Auth\AuthHandlerInterface;

interface AuthInterface
{
    public function isGuest();

    public function loggedIn();

    public function attempt(array $credentials);

    public function login(UserInterface $user);

    public function logout();

    /**
     * Get a unique identifier for the auth session value.
     *
     * @return string
     */
    public function getName();

    /**
     * @param \Tomahawk\Auth\AuthHandlerInterface $handler
     * @return $this
     */
    public function setHandler(AuthHandlerInterface $handler);

    /**
     * @return \Tomahawk\Auth\AuthHandlerInterface
     */
    public function getHandler();

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getUser();
}

