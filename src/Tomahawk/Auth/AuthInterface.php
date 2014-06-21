<?php

namespace Tomahawk\Auth;

use Tomahawk\Auth\AuthHandlerInterface;

interface AuthInterface
{
    /**
     * @param AuthHandlerInterface $handler
     * @return $this;
     */
    function setHandler(AuthHandlerInterface $handler);

    /**
     * @return AuthHandlerInterface|null
     */
    function getHandler();

    /**
     * @return bool
     */
    function isGuest();

    /**
     * @return bool
     */
    function loggedIn();

    function getUser();

    function attempt(array $credentials);
}