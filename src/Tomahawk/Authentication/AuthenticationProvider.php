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

use Tomahawk\Authentication\Exception\BadCredentialsException;
use Tomahawk\Authentication\Exception\UserNotFoundException;
use Tomahawk\Authentication\User\CredentialsInterface;
use Tomahawk\Authentication\User\UserInterface;
use Tomahawk\Authentication\User\UserProviderInterface;
use Tomahawk\Authentication\Storage\StorageInterface;
use Tomahawk\Authentication\Encoder\PasswordEncoderInterface;

/**
 * Class AuthManager
 *
 * @package Tomahawk\Authentication
 */
class AuthenticationProvider implements AuthenticationProviderInterface
{
    const DEFAULT_STORAGE_KEY = '_th2_auth';

    /**
     * @var string
     */
    private $storageKey;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var UserInterface|null
     */
    private $user;

    /**
     * @var bool
     */
    private $loggedIn;

    public function __construct(
        UserProviderInterface $userProvider,
        PasswordEncoderInterface $passwordEncoder,
        StorageInterface $storage,
        $storageKey = self::DEFAULT_STORAGE_KEY

    )
    {
        $this->userProvider = $userProvider;
        $this->passwordEncoder = $passwordEncoder;
        $this->storage = $storage;
        $this->storageKey = $storageKey;
    }

    /**
     * Try and authorize a user based on credentials
     *
     * @param CredentialsInterface $credentials
     * @return UserInterface
     * @throws UserNotFoundException
     * @throws BadCredentialsException
     */
    public function authorize(CredentialsInterface $credentials)
    {
        // Check if user can be found
        if ( ! $user = $this->userProvider->findUserByUsername($credentials->getUsername())) {
            throw new UserNotFoundException(sprintf('User "%s" not found', $credentials->getUsername()));
        }

        // Check if password is valid
        if ( ! $this->passwordEncoder->isPasswordValid($user->getPassword(), $credentials->getPassword(), $user->getSalt())) {
            throw new BadCredentialsException(sprintf('Password for user "%s" was incorrect', $credentials->getUsername()));
        }

        // Put user in the auth storage
        $this->storage->setIdentifier($this->storageKey, $user->getUsername());

        $this->user = $user;

        $this->loggedIn = true;

        return $user;
    }

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        if (null === $this->loggedIn) {
            $this->user = $this->loadUser();
            $this->loggedIn = $this->user instanceof UserInterface;
        }

        return $this->loggedIn;
    }

    /**
     * Check if user is a guest
     *
     * @return bool
     */
    public function isGuest()
    {
        return ! $this->isLoggedIn();
    }

    /**
     * Login a given user
     *
     * @param UserInterface $user
     * @throws UserNotFoundException
     */
    public function login(UserInterface $user)
    {
        // Check if user can be found.
        // Stops trying to login a user that hasn't been added to database yet
        if ( ! $this->userProvider->findUserByUsername($user->getUsername())) {
            throw new UserNotFoundException(sprintf('User "%s" not found', $user->getUsername()));
        }

        // Put user in the auth storage
        $this->storage->setIdentifier($this->storageKey, $user->getUsername());

        $this->user = $user;

        $this->loggedIn = true;
    }

    /**
     * Logout a user
     */
    public function logout()
    {
        $this->storage->removeIdentifier($this->storageKey);
        $this->user = null;
        $this->loggedIn = false;
    }

    /**
     * Get user that is logged in
     *
     * @return UserInterface|null
     */
    public function getUser()
    {
        if ($this->isLoggedIn()) {
            return $this->user;
        }
    }

    /**
     * Load user
     *
     * @return null|UserInterface
     */
    private function loadUser()
    {
        $user = null;

        if ($username = $this->storage->getIdentifier($this->storageKey)) {
            $user = $this->userProvider->findUserByUsername($username);
        }

        if ($user) {
            $this->loggedIn = true;
        }

        return $user;
    }
}
