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

use Tomahawk\Authentication\Factory\GuardFactory;
use Tomahawk\Authentication\Guard\GuardInterface;
use Tomahawk\Authentication\User\CredentialsInterface;
use Tomahawk\Authentication\User\UserInterface;

/**
 * Class AuthenticationProvider
 *
 * @package Tomahawk\Authentication
 */
class AuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @var GuardFactory
     */
    protected $guardFactory;

    /**
     * @var string
     */
    protected $defaultGuard;

    /**
     * @var GuardInterface
     */
    protected $createdGuards = [];

    public function __construct(GuardFactory $guardFactory, string $defaultGuard)
    {
        $this->guardFactory = $guardFactory;
        $this->defaultGuard = $defaultGuard;
    }

    /**
     * Try and authorize a user based on credentials
     *
     * @param CredentialsInterface $credentials
     * @param string|null $guard
     * @return UserInterface
     */
    public function authorize(CredentialsInterface $credentials, string $guard = null)
    {
        $guard = $this->guard($guard);

        return $guard->authorize($credentials);
    }

    /**
     * Check if user is logged in
     *
     * @param null|string $guard
     * @return bool
     */
    public function isLoggedIn(string $guard = null)
    {
        $guard = $this->guard($guard);

        return $guard->isLoggedIn();
    }

    /**
     * Check if user is a guest
     *
     * @param null|string $guard
     * @return bool
     */
    public function isGuest(string $guard = null)
    {
        return ! $this->isLoggedIn($guard);
    }

    /**
     * Login a given user
     *
     * @param UserInterface $user
     * @param null|string $guard
     */
    public function login(UserInterface $user, string $guard = null)
    {
        $guard = $this->guard($guard);

        $guard->login($user);
    }

    /**
     * Logout a user
     * @param null|string $guard
     */
    public function logout(string $guard = null)
    {
        $this->guard($guard)->logout();
    }

    /**
     * Get user that is logged in
     *
     * @param null|string $guard
     * @return UserInterface|null
     */
    public function getUser(string $guard = null)
    {
        if ($this->guard($guard)->isLoggedIn()) {
            return $this->guard($guard)->getUser();
        }

        return null;
    }

    /**
     * Load user
     *
     * @param null|string $guard
     * @return null|UserInterface
     */
    public function loadUser(string $guard = null)
    {
        $user = $this->guard($guard)->loadUser();

        return $user;
    }

    /**
     * @param null|string $guard
     * @return GuardInterface
     */
    public function guard(string $guard = null)
    {
        $guard = $guard ?? $this->defaultGuard;

        if ( ! isset($this->createdGuards[$guard])) {
            return $this->createdGuards[$guard] = $this->createGuard($guard);
        }

        return $this->createdGuards[$guard];
    }

    /**
     * @param string $guard
     * @return GuardInterface
     */
    protected function createGuard(string $guard)
    {
        return $this->guardFactory->make($guard);
    }
}
