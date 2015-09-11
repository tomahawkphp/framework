<?php

namespace Tomahawk\Bundle\DoctrineBundle\Auth\Handlers;

use Tomahawk\Auth\AuthHandlerInterface;
use Tomahawk\Auth\UserInterface;
use Tomahawk\Hashing\HasherInterface;
use Tomahawk\Bundle\DoctrineBundle\RegistryInterface;

class DoctrineAuthHandler implements AuthHandlerInterface
{
    /**
     * @var \Tomahawk\Hashing\HasherInterface
     */
    protected $hasher;

    /**
     * @var \Tomahawk\Bundle\DoctrineBundle\RegistryInterface
     */
    protected $doctrine;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var string
     */
    protected $usernameField;

    /**
     * @var string
     */
    protected $passwordField;

    public function __construct(
        HasherInterface $hasher,
        RegistryInterface $doctrine,
        $model,
        $usernameField,
        $passwordField = null
    )
    {
        $this->hasher = $hasher;
        $this->doctrine = $doctrine;
        $this->model = $model;
        $this->usernameField = $usernameField;
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
        $repo = $this->doctrine->getRepository($this->model);
        return $repo->find($identifier);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Tomahawk\Auth\UserInterface|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $repo = $this->doctrine->getRepository($this->model);

        unset($credentials[$this->passwordField]);

        return $repo->findOneBy($credentials);
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
}
