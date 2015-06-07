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

interface AuthInterface
{
    /**
     * Check if user is a quest
     *
     * @return bool
     */
    function isGuest();

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    function loggedIn();

    /**
     * Attempt to login user from credentials
     *
     * @param array $credentials
     * @return bool
     */
    function attempt(array $credentials);

    /**
     * Login a given user
     *
     * @param UserInterface $user
     */
    public function login(UserInterface $user);

    /**
     * Logout a user
     */
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

