<?php

namespace Tomahawk\Authentication\Guard;

use Tomahawk\Authentication\Encoder\PasswordEncoderInterface;
use Tomahawk\Authentication\Exception\BadCredentialsException;
use Tomahawk\Authentication\Exception\UserNotFoundException;
use Tomahawk\Authentication\User\CredentialsInterface;
use Tomahawk\Authentication\User\UserInterface;
use Tomahawk\Authentication\User\UserProviderInterface;
use Tomahawk\Session\SessionInterface;

/**
 * Class SessionGuard
 * @package Tomahawk\Authentication\Guard
 */
class SessionGuard implements GuardInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var UserProviderInterface
     */
    protected $userProvider;

    /**
     * @var UserInterface|null
     */
    protected $user;

    /**
     * @var bool
     */
    protected $loggedIn;

    /**
     * @var PasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * SessionGuard constructor.
     * @param string $name
     * @param SessionInterface $session
     * @param UserProviderInterface $userProvider
     * @param PasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        string $name,
        SessionInterface $session,
        UserProviderInterface $userProvider,
        PasswordEncoderInterface $passwordEncoder
    )
    {
        $this->name = $name;
        $this->session = $session;
        $this->userProvider = $userProvider;
        $this->passwordEncoder = $passwordEncoder;
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
        $this->session->set($this->name, $user->getUsername());

        $this->user = $user;

        $this->loggedIn = true;
    }

    /**
     * Logout a user
     */
    public function logout()
    {
        $this->session->remove($this->name);
        $this->user = null;
        $this->loggedIn = false;
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
        $this->session->set($this->name, $user->getUsername());

        $this->user = $user;

        $this->loggedIn = true;

        return $user;
    }

    /**
     * @return null|UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Load user
     *
     * @return null|UserInterface
     */
    public function loadUser()
    {
        $user = null;

        if ($username = $this->session->get($this->name)) {
            $user = $this->userProvider->findUserByUsername($username);
        }

        if ($user) {
            $this->loggedIn = true;
        }

        return $user;
    }
}