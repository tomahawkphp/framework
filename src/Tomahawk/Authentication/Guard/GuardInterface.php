<?php

namespace Tomahawk\Authentication\Guard;

use Tomahawk\Authentication\Exception\BadCredentialsException;
use Tomahawk\Authentication\Exception\UserNotFoundException;
use Tomahawk\Authentication\User\CredentialsInterface;
use Tomahawk\Authentication\User\UserInterface;

/**
 * Class GuardInterface
 * @package Tomahawk\Authentication\Guard
 */
interface GuardInterface
{
    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public function isLoggedIn();

    /**
     * Check if user is a guest
     *
     * @return bool
     */
    public function isGuest();

    /**
     * Login a given user
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
     * Try and authorize a user based on credentials
     *
     * @param CredentialsInterface $credentials
     * @return UserInterface
     * @throws UserNotFoundException
     * @throws BadCredentialsException
     */
    public function authorize(CredentialsInterface $credentials);

    /**
     * @return null|UserInterface
     */
    public function getUser();

    /**
     * Load user
     *
     * @return null|UserInterface
     */
    public function loadUser();
}
