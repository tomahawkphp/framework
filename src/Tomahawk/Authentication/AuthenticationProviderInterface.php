<?php
/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 19/04/2018
 * Time: 21:34
 */

namespace Tomahawk\Authentication;

use Tomahawk\Authentication\Guard\GuardInterface;
use Tomahawk\Authentication\User\CredentialsInterface;
use Tomahawk\Authentication\User\UserInterface;


/**
 * Class AuthenticationProvider
 *
 * @package Tomahawk\Authentication
 */
interface AuthenticationProviderInterface
{
    /**
     * Try and authorize a user based on credentials
     *
     * @param CredentialsInterface $credentials
     * @param string|null $guard
     * @return UserInterface
     */
    public function authorize(CredentialsInterface $credentials, string $guard = null);

    /**
     * Check if user is logged in
     *
     * @param null|string $guard
     * @return bool
     */
    public function isLoggedIn(string $guard = null);

    /**
     * Check if user is a guest
     *
     * @param null|string $guard
     * @return bool
     */
    public function isGuest(string $guard = null);

    /**
     * Login a given user
     *
     * @param UserInterface $user
     * @param null|string $guard
     */
    public function login(UserInterface $user, string $guard = null);

    /**
     * Logout a user
     * @param null|string $guard
     */
    public function logout(string $guard = null);

    /**
     * Get user that is logged in
     *
     * @param null|string $guard
     * @return UserInterface|null
     */
    public function getUser(string $guard = null);

    /**
     * Load user
     *
     * @param null|string $guard
     * @return null|UserInterface
     */
    public function loadUser(string $guard = null);

    /**
     * @param null|string $guard
     * @return GuardInterface
     */
    public function guard(string $guard = null);
}
