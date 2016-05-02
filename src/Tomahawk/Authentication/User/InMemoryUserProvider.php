<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Authentication\User;

/**
 * Class InMemoryUserProvider
 *
 * A simple class used for testing authentication
 *
 * Inspired by the Symfony InMemoryUserProvider
 *
 * @package Tomahawk\Authenticationentication\User
 */
class InMemoryUserProvider implements UserProviderInterface
{
    /**
     * @var array
     */
    private $users;

    public function __construct(array $users)
    {
        foreach ($users as $username => $attributes) {
            $password = isset($attributes['password']) ? $attributes['password'] : null;
            $this->createUser(new User($username, $password));
        }
    }

    /**
     * Find user by username
     *
     * @param $username
     * @return UserInterface|null
     */
    public function findUserByUsername($username)
    {
        return isset($this->users[strtolower($username)]) ?
            $this->users[strtolower($username)] : null;
    }

    /**
     * @param UserInterface $user
     */
    public function createUser(UserInterface $user)
    {
        if (isset($this->users[strtolower($user->getUsername())])) {
            throw new \InvalidArgumentException('User has already been added');
        }

        $this->users[strtolower($user->getUsername())] = $user;
    }
}
