<?php

namespace Tomahawk\Authentication\Test;

use Tomahawk\Authentication\User\UserInterface;
use Tomahawk\Authentication\User\UserProviderInterface;

/**
 * Class TestUserProvider
 * @package Tomahawk\Authentication\Test
 */
class TestUserProvider implements UserProviderInterface
{
    /**
     * @var string
     */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Find user by username
     *
     * @param $username
     * @return UserInterface|null
     */
    public function findUserByUsername($username)
    {
        return null;
    }
}