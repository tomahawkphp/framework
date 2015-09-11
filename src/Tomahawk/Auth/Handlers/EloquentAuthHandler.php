<?php

namespace Tomahawk\Auth\Handlers;

use Tomahawk\Auth\AuthHandlerInterface;
use Tomahawk\Auth\UserInterface;
use Tomahawk\Hashing\HasherInterface;

class EloquentAuthHandler implements AuthHandlerInterface
{
    /**
     * @var string
     */
    protected $model;

    /**
     * @var \Tomahawk\Hashing\HasherInterface
     */
    protected $hasher;

    /**
     * @var string
     */
    protected $passwordField;

    public function __construct(HasherInterface $hasher, $model, $passwordField = null)
    {
        $this->hasher = $hasher;
        $this->model = $model;
        $this->passwordField = $passwordField ?: 'password';
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Tomahawk\Auth\UserInterface|null
     */
    public function retrieveById($identifier)
    {
        return $this->createModel()->newQuery()->find($identifier);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Tomahawk\Auth\UserInterface|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $query = $this->createModel()->newQuery();

        foreach ($credentials as $key => $value) {
            if ($key !== $this->passwordField) {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Tomahawk\Auth\UserInterface  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserInterface $user, array $credentials)
    {
        $plain = $credentials[$this->passwordField];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
    }

    /**
     * @return mixed
     */
    public function getModelName()
    {
        return $this->model;
    }
}

