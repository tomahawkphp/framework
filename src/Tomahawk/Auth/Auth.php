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

use Tomahawk\Session\SessionInterface;
use Tomahawk\Auth\AuthHandlerInterface;

class Auth implements AuthInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var AuthHandlerInterface
     */
    protected $handler;

    /**
     * @var bool
     */
    protected $loggedOut = false;

    /**
     * @var UserInterface|null
     */
    protected $user;

    protected $account;

    public function __construct(SessionInterface $session, AuthHandlerInterface $handler)
    {
        $this->session = $session;
        $this->handler = $handler;
    }

    function isGuest()
    {
        return ! $this->loggedIn();
    }

    function loggedIn()
    {
        return ! is_null($this->getUser());
    }

    function attempt(array $credentials)
    {
        if (!$user = $this->handler->retrieveByCredentials($credentials))
        {
            return false;
        }

        if ($this->handler->validateCredentials($user, $credentials))
        {
            $this->login($user);
            return true;
        }

        return false;
    }

    public function login(UserInterface $user)
    {
        $name = $this->getName();
        $id = $user->getAuthIdentifier();

        $this->session->set($name, $id);
        $this->setUser($user);
    }

    public function logout()
    {
        $this->user = null;
        $this->loggedOut = true;
    }

    /**
     * Get a unique identifier for the auth session value.
     *
     * @return string
     */
    public function getName()
    {
        return 'login_'.md5($this->account);
    }

    /**
     * @param \Tomahawk\Auth\AuthHandlerInterface $handler
     * @return $this
     */
    public function setHandler(AuthHandlerInterface $handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * @return \Tomahawk\Auth\AuthHandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        if ($this->loggedOut) {
            return;
        }

        $user = null;

        if (!$id = $this->session->get($this->getName())) {
            return null;
        }

        $user = $this->handler->retrieveById($id);

        return $this->user = $user;
    }
}

