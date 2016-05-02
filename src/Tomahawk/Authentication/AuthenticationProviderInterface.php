<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Authentication;

use Tomahawk\Authentication\User\UserInterface;
use Tomahawk\Authentication\User\CredentialsInterface;
use Tomahawk\Authentication\Exception\BadCredentialsException;
use Tomahawk\Authentication\Exception\UserNotFoundException;

/**
 * Interface AuthenticationProviderInterface
 *
 * @package Tomahawk\Authentication
 */
interface AuthenticationProviderInterface
{
    /**
     * Try and authorize a user based on credentials
     *
     * @param CredentialsInterface $credentials
     * @return UserInterface
     * @throws UserNotFoundException
     * @throws BadCredentialsException
     */
    public function authorize(CredentialsInterface $credentials);

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    function isLoggedIn();

    /**
     * Check if user is a guest
     *
     * @return bool
     */
    function isGuest();

    /**
     * Login a given user
     *
     * Throws an exception
     *
     * @param UserInterface $user
     * @throws UserNotFoundException
     */
    public function login(UserInterface $user);

    /**
     * Logout a user
     */
    public function logout();

    /**
     * Get user that is logged in
     *
     * @return UserInterface|null
     */
    public function getUser();
}
