<?php

namespace Tomahawk\Illuminate\Authentication\User;

use Illuminate\Database\Eloquent\Model;
use Tomahawk\Authentication\User\UserInterface;
use Tomahawk\Authentication\User\UserProviderInterface;

/**
 * Class EloquentUserProvider
 * @package Tomahawk\Illuminate\Authentication\User
 */
class EloquentUserProvider implements UserProviderInterface
{
    /**
     * @var string
     */
    private $model;

    /**
     * @var string
     */
    private $username;

    public function __construct(string $model, string $username)
    {
        $this->model = $model;
        $this->username = $username;
    }

    /**
     * Find user by username
     *
     * @param $username
     * @return UserInterface|null
     */
    public function findUserByUsername($username)
    {
        /** @var UserInterface|null $result */
        $result = $this->createClass()
            ->where($this->username, '=', $username)
        ;

        return $result;
    }

    /**
     * @return Model
     */
    protected function createClass()
    {
        return new $this->model();
    }
}
